<?php
/**
 * Created by PhpStorm.
 * User: ziann
 * Date: 16/3/24
 * Time: 上午11:07
 */

namespace App\Models;


use LaravelArdent\Ardent\Ardent;
use Cache;
use Str;

class BaseModel extends Ardent
{

    //缓存级别
    const CACHE_LEVEL_NONE = 0;
    const CACHE_LEVEL_FIRST = 1;
    const CACHE_LEVEL_SECOND = 2;
    const CACHE_LEVEL_THIRD = 3;
    //默认缓存等级0
    protected static $cacheLevel = self::CACHE_LEVEL_NONE;
    //缓存有效时间,0是永久有效
    protected static $cacheMinutes = 0;
    //是否使用父类的缓存
    protected static $cacheUseParentClass = false;
    //缓存级别的驱动
    public static $cacheDrivers = [
        self::CACHE_LEVEL_FIRST => 'memcached',
        self::CACHE_LEVEL_SECOND => 'redis',
        self::CACHE_LEVEL_THIRD => 'mango',
    ];
    //缓存级别的对应
    public static $validCacheLevels = [
        self::CACHE_LEVEL_NONE => 'none',
        self::CACHE_LEVEL_FIRST => 'first',
        self::CACHE_LEVEL_SECOND => 'second',
        self::CACHE_LEVEL_THIRD => 'third'
    ];
    //面包屑名字
    protected $resourceName = '';
    //是否支持软删除
    protected $softDelete = false;
    //实例化是过去的模型字段
    protected $defaultColumns = ['*'];
    //是否支持批量处理
    protected $enableBatchAction = false;
    //表单验证的消息
    protected $validatorMessages = [];
    //隐藏的字段
    protected $hidden = [];
    //可见的字段
    protected $visible = [];

    //模型是否是树状结构
    public static $treeable = false;
    //树状结构是否移动
    public $treeNodeMoved = false;
    //树性模型祖先id字段
    public static $foreFatherIdColumn = '';
    //树形模型祖先字段
    public static $foreFatherColumn = '';
    //列表页展现的字段数组
    public static $columnForList = [];
    //默认的语言包
    public static $defaultLangPack;
    //编辑页忽略的字段
    public static $ignoreColumnsInEdit = [];
    //创建页忽略的字段
    public static $ignoreColumnsInCreate = [];
    //详情页面中需要忽略的字段数组
    public static $ignoreColumnsInView = [];
    //编辑页面中显示为只读的字段数组
    public static $readonlyColumnsInEdit = [];
    //是否启用手动排序
    public static $sequencable = false;
    //手动排序字段
    public static $sequenceColumn = 'sequence';

    //index页虚拟列友好显示处理
    public static $listColumnMaps = [];
    //view视图显示时使用，用于某些列有特定格式，且定义了虚拟列的情况
    public static $viewColumnMaps = [];
    //需要显示页面小计的字段数组(用于金额处理)
    public static $totalColumns = [];
    //总计数据中的比例列字段数组
    public static $totalRateColumns = [];
    //加粗显示的字段数组
    public static $weightFields = [];
    //显示不同颜色的字段数组
    public static $classGradeFields = [];
    //显示浮点型的字段数组
    public static $floatDisplayFields = [];
    //不支持orderby按钮的字段数组，供列表页使用
    public static $noOrderByColumns = [];
    //下拉列表框字段配置 select
    public static $htmlSelectColumns = [];
    //编辑框字段配置
    public static $htmlTextAreaColumns = [];
    //number字段数组
    public static $htmlNumberColumns = [];
    //显示原始数字的字段数组
    public static $htmlOriginalNumberColumns = [];
    //金额字段的存储精度
    public static $amountAccuracy = 0;
    //Columns
    public static $originalColumns;
    //显示为链接的字段配置，键为文本列，值为URL列
    public static $linkColumns = [];
    //Column Settings
    public $columnSettings = [];
    //排序字段数组
    public $orderColumns = [];
    //分组查询数组
    public $groupByColumns = [];
    //标题字段
    public static $titleColumn = 'title';
    //列表页面的主要查询参数字段
    public static $mainParamColumn = 'parent_id';
    // 字段类型数组
    public $columnTypes = [];


    public function __construct($attribute = [])
    {
        parent::__construct($attribute);
//        $this->compileLangPack();
    }

    protected function getFriendlyCreatedAtAttribute()
    {
        return friendly_date($this->created_at);
    }

    protected function getFriendlyUpdatedAtAttribute()
    {
        return friendly_date($this->updated_at);
    }

    protected function getFriendlyDeleteedAtAttribute()
    {
        return friendly_date($this->deleted_at);
    }

    //cache相关

    /** [删除缓存]
     * @param $sCacheKey string 缓存key
     * @return bool
     */
    public static function deleteCache($sCacheKey)
    {
        if (static::$cacheLevel == static::CACHE_LEVEL_NONE) {
            return true;
        }
        //得到cacheKey
        $sKey = static::generateCacheKey($sCacheKey);
        //设置缓存驱动
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        return !Cache::has($sKey) or Cache::forget($sKey);
    }

    /** [生成缓存key]
     * @param $key string
     * @return string
     */
    public static function generateCacheKey($key)
    {
        return static::getCachePrefix() . $key;
    }

    /** [获取缓存key前缀 ]
     * @param bool $bPlural 是否采用复数形式
     * @return string
     */
    public static function getCachePrefix($bPlural = false)
    {
        $sClass = static::getRealClassCache();
        !$bPlural or $sClass = Str::plural($sClass);
        return config('cache.prefix') . $sClass . '-';
    }

    /**[组建复杂的查询条件]
     * @param array $aConditions
     */
    public function doWhere($aConditions = [])
    {
        $oQuery = static::where('id', '>', 0);
        if (!is_array($aConditions) || !count($aConditions)) return $oQuery;

        foreach ($aConditions as $column => $aCondition) {
            list($cons, $tmp) = $aCondition;
            switch ($cons) {
                case '=':
                    $oQuery = is_null($tmp) ? $oQuery->whereNull($column) : $oQuery->where($column, '=', $tmp);
                    break;
                case 'in':
                    $tmp = is_array($tmp) ? explode(',', $tmp) : $tmp;
                    $oQuery = $oQuery->whereIn($column, $tmp);
                    break;
                case 'between':
                    $tmp = is_array($tmp) ? explode(',', $tmp) : $tmp;
                    $oQuery = $oQuery->whereBetween($column, $tmp);
                    break;
                case '>':
                case '>=':
                case '<':
                case '<=':
                case '!=':
                case '<>':
                case 'like':
                    $oQuery = is_null($tmp) ? $oQuery->whereNotNull($column) : $oQuery->where($column, $cons, $tmp);
                    break;
            }
        }
        return $oQuery;
    }

    /** [组装排序条件]
     * @param object $oQuery
     * @param array $aOrderBy
     * @return object
     */
    public function doOrderBy($oQuery = null, $aOrderBy = [])
    {
        !is_null($oQuery) or $oQuery = $this;
        count($aOrderBy) or $aOrderBy = $this->orderColumns;
        foreach ($aOrderBy as $key => $value) {
            $oQuery = $oQuery->orderBy($key, $value);
        }
        return isset($oQuery) ? $oQuery : $this;
    }

    /** 分组查询
     * @param object $oQuery
     * @param array $aGroupBy
     * @return object
     */
    public function doGroupBy($oQuery = null, $aGroupBy = [])
    {
        !is_null($oQuery) or $oQuery = $this;
        count($aGroupBy) or $aOrderBy = $this->groupByColumns;
        foreach ($aGroupBy as $group) {
            $oQuery = $oQuery->groupBy($group);
        }
        return isset($oQuery) ? $oQuery : $this;
    }

    /** [获取树状结构]
     * @param $aTree array
     * @param int $iParent_id 父级id
     * @param array $aConditions 筛选条件
     * @param array $aOrderBy 排序方式
     * @param string $sPrefix 子集前缀
     * @return array | boolean
     */
    public static function getTree(& $aTree, $iParent_id = null, $aConditions = [], $aOrderBy = [], $sPrefix = '--')
    {
        if (!static::$treeable) return false;
        static $deep = 0;
        $aFields = ['id', static::$titleColumn];

        $obj = new static;
        $aConditions['parent_id'] = ['=', $iParent_id];
        $oQuery = $obj->doWhere($aConditions);
        $oQuery = $oQuery->doOrderBy($oQuery, $aOrderBy);
        $deep++;
        $aModels = $oQuery->get($aFields);

        foreach ($aModels as $oModel) {
            $sTitle = empty($sPrefix) ? $oModel->{static::$titleColumn} : str_repeat($sPrefix, ($deep - 1)) . $oModel->{static::$titleColumn};
            $aTree[$oModel->id] = $sTitle;
            $obj->getTree($aTree, $oModel->id, $aConditions, $aOrderBy, $sPrefix);
        }
//        $deep--;
    }

    /** [设置父级id、父级name 方法]
     * @param $iParentId
     */
    public function setParentIdAttribute($iParentId)
    {
        $this->attributes['parent_id'] = $iParentId;

        if ($iParentId) {
            $oParentModel = $this->find($this->parent_id);
            $this->parent = $oParentModel->{static::$titleColumn};
        } else {
            $this->parent = '';
        }
        if (static::$foreFatherIdColumn) {
            static::setForefather();
        }
    }

    /**
     * [设置祖先id和祖先名称]
     */
    public function setForefather()
    {

        $sColumn = static::$foreFatherIdColumn;
        $oParentModel = $this->find($this->parent_id);
        $this->$sColumn = empty($oParentModel->$sColumn) ? $this->parent_id : ($oParentModel->$sColumn . ',' . $this->parent_id);

        if ($this->$sColumn) {
            if ($this->parent_id) {
                if ($sForeColumn = static::$foreFatherColumn) {
                    $this->$sForeColumn = empty($oParentModel->$sForeColumn) ? $oParentModel->{static::$titleColumn} : ($oParentModel->$sForeColumn . ',' . $oParentModel->{static::$titleColumn});
                }
            }
        } else {
            $this->attributes[static::$foreFatherIdColumn] = '';
            if ($sForeColumn = static::$foreFatherColumn) {
                $this->attributes[$sForeColumn] = '';
            }
        }
    }

    /** [parent_id移动时改变]
     * @return bool
     */
    protected function beforeValidate()
    {
        if (static::$treeable) {
            if ($this->treeNodeMoved = $this->isDirty('parent_id')) {
                $this->parent_id = $this->parent_id;
            }
        }
        return true;
    }

    /** [parent_id更换场景处理]
     * @param $oSaveModel
     * @return bool
     */
    protected function afterSave($oSaveModel)
    {
        $this->deleteCache($this->id);
        $bSucc = true;

        if ($oSaveModel::$treeable && $oSaveModel->treeNodeMoved) {

            $aData = static::getSubObjectArray($this->id);
            foreach ($aData as $oModel) {
                $oModel->parent_id = $this->id;
                if (!$bSucc = $oModel->save()) {
                    break;
                }
            }
        }
        return $bSucc;
    }

    /**
     * 更新时删除缓存
     */
    protected function afterUpdate()
    {
        $this->deleteCache($this->id);
    }

    /** 删除时删除缓存
     * @param $oDeleteModel
     * @return bool
     */
    protected function afterDelete($oDeleteModel)
    {
        $this->deleteCache($oDeleteModel->id);
        return true;
    }

    /** [加入缓存的find方法]
     * @param int $id
     * @param array $columns
     * @return bool|Ardent|\LaravelArdent\Ardent\Collection|static
     */
    public static function find($id, $columns = ['*'])
    {
        if (static::$cacheDrivers[static::$cacheLevel] == self::CACHE_LEVEL_NONE) {
            return parent::find($id, $columns);
        }
        Cache::setDafaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        $sKey = static::generateCacheKey($id);
        if ($aAttributes = Cache::get($sKey)) {
            $obj = new static;
            $obj = $obj->newFromBuilder($aAttributes);
        } else {
            $obj = parent::find($id);
            if (is_object($obj)) {
                return false;
            }
            if (static::$cacheMinutes) {
                Cache::put($sKey, $obj->getAttributes(), static::$cacheMinutes);
            } else {
                Cache::forver($sKey, $obj->getAttributes());
            }

        }
        // 移除不需要的属性
        if (is_array($columns) && !empty($columns) && !in_array('*', $columns)) {

            $aAllColumns = array_keys($obj->attributes);
            $aExpertColumns = array_diff($aAllColumns, $columns);
            foreach ($aExpertColumns as $sColumn) {
                unset($obj->attributes[$sColumn]);
            }
        }
        return $obj;
    }

    /**[返回db的所有table的所有字段以及字段类型]
     * @return array
     */
    public function & getColumnTypes()
    {
        if (empty($this->columnTypes)) {
            $sDatabase = $this->getConnection()->getConfig('database');
            $sSql = "select column_name,data_type from information_schema.columns where table_schema='$sDatabase' and table_name='{$this->table}' order by ordinal_position;";
            $aColumns = DB::select($sSql);
            $ret = [];

            foreach ($aColumns as $aColumn) {
                $ret[$aColumn->column_name] = $aColumn->data_type;
            }
            $this->columnTypes = $ret;
        }
        return $this->columnTypes;
    }

    /** [获取所有父级]
     * @param int $iParent_id 父级id
     * @param array $aConditions 搜索条件
     * @param array $aOrderBy 排序条件
     * @return array
     */
    public static function getSubObjectArray($iParent_id = null, $aConditions = [], $aOrderBy = [])
    {
        $aConditions['parent_id'] = ['=', $iParent_id];
        $obj = new static;
        $oQuery = $obj->doWhere($aConditions);
        $oQuery = $oQuery->doOrderBy($aOrderBy);
        $oModel = $oQuery->get();
        $ret = [];

        foreach ($oModel as $model) {
            $ret[$model->id] = $model;
        }
        return $ret;
    }

    /** [根据制定的条件获取首条记录]
     * @param array $aConditions
     * @return mixed
     */
    public static function getObjectByParams(array $aConditions = ['*'])
    {
        return static::getObjectCollectionByParams($aConditions)->first();
    }

    /** [根据制定的条件获取所有记录]
     * @param array $aConditions
     * @return mixed
     */
    public static function getObjectCollectionByParams(array $aConditions = ['*'])
    {
        foreach ($aConditions as $key => $item) {
            if (isset($oQuery) && is_object($oQuery)) {
                $oQuery = $oQuery->where($key, $item);
            } else {
                $oQuery = static::where($key, $item);
            }
        }
        return $oQuery->get();
    }

    /**[获取所有指定字段的值数组,不穿入字段并使用主键,此方法等效getTitleList]
     * @param string $sColumn 字段名
     * @param array $aConditions 搜索条件
     * @param array $aOrderBy 排序条件
     * @param bool $bUsePrimaryKey 是否使用主键
     * @return array
     */
    public function getValueListArray($sColumn = null, $aConditions = [], $aOrderBy = [], $bUsePrimaryKey = false)
    {
        $sColumn or $sColumn = static::$titleColumn;
        $aFields = $bUsePrimaryKey ? ['id', $sColumn] : $sColumn;
        $aOrderBy or $aOrderBy = [$sColumn, 'asc'];
        $obj = new static;
        $oQuery = $obj->doWhere($aConditions);
        $oQuery = $oQuery->doOrderBy($oQuery, $aOrderBy);
        $aModels = $oQuery->get($aFields);

        $ret = [];
        foreach ($aModels as $oModel) {
            $sFiledId = $bUsePrimaryKey ? $oModel->id : $sColumn;
            $ret[$sFiledId] = $oModel->{$sColumn};
        }
        return $ret;
    }

    /** [获取id和titleColumn组成数组 ，数据结构 id=>name]
     * @param bool $bOrderByTitle
     * @return array
     */
    public static function & getTitleList($bOrderByTitle = true)
    {

        $aFields = ['id', static::$titleColumn];
        $sOrderColumn = $bOrderByTitle ? static::$titleColumn : 'id';
        //默认采用正序
        $oModels = static::orderBy($sOrderColumn, 'asc')->get($aFields);
        $ret = [];
        foreach ($oModels as $model) {
            $ret[$model->id]->$model->{static::$titleColumn};
        }
        return $ret;
    }

    /** [获取类名]
     * @return string
     */
    protected static function getRealClassCache()
    {
        $sClass = get_called_class();
        !static::$cacheUseParentClass or $sClass = get_parent_class($sClass);
        $sShortClass = substr($sClass, (strpos($sClass, '\\') - strlen($sClass) + 1));
        return $sShortClass;
    }

    /**[拼装表单错误提示语]
     * @return string
     */
    public function & getValidationErrorString()
    {
        $aMsg = $this->exists ? [$this->id, ':'] : [static::$titleColumn, ':'];

        foreach ($this->validationErrors->toArray() as $sColumn => $aMessage) {
            $aMsg[] = $sColumn . ': ' . implode(',', $aMessage);
        }
        $sErrorMsg = implode(' ', $aMsg);

        return $sErrorMsg;
    }
    /**
     * [getFormattedNumberForHtml 获取格式化后的数字，用于金额显示]
     * @param  string  $sColumn   [要格式化的字段]
     * @param  boolean $bTruncate [是否去除多余小数]
     * @return int                [格式化后的数字]
     */
    protected function getFormattedNumberForHtml($sColumn, $bTruncate = false)
    {
        $iAccuracy = static::$htmlNumberColumns[$sColumn] ? static::$htmlNumberColumns[$sColumn] : static::$amountAccuracy;
        $fNumber = $this->{$sColumn};
        $iBase = pow(10, $iAccuracy);
        if ($bTruncate) {
            return number_format(intval($fNumber * $iBase) / $iBase, $iAccuracy);
        } else {
            return number_format($fNumber, $iAccuracy);
        }
    }
}