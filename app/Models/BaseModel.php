<?php
/**
 * Created by PhpStorm.
 * User: ziann
 * Date: 16/3/24
 * Time: 上午11:07
 */

namespace App\Models;


use LaravelArdent\Ardent\Ardent;

class BaseModel extends Ardent {

    protected $table='';
    protected $fillable=[];
    protected $modelName='';

    /**
     * [getValueListArray     获取名值数组]
     *
     * @param string $sColumn         [要获取值的字段]
     * @param array  $aConditions     [查询条件]
     * @param array  $aOrderBy        [排序条件]
     * @param bool   $bUsePrimaryKey  [是否使用主键值做数组的key]
     * @return array                  [符合条件的键值对数组]
     */
    function getValueListArray($sColumn = null, $aConditions = [], $aOrderBy = [], $bUsePrimaryKey = false) {
        $sColumn or $sColumn = static::$titleColumn;
        $aColumns = $bUsePrimaryKey ? [ 'id', $sColumn] : [ $sColumn];
        $aOrderBy or $aOrderBy = [ $sColumn => 'asc'];
        $oQuery = $this->doWhere($aConditions);
        $oQuery = $this->doOrderBy($oQuery, $aOrderBy);
        $oModels = $oQuery->get($aColumns);
        $data = [];
        foreach ($oModels as $oModel) {
            $sKeyField = $bUsePrimaryKey ? $oModel->id : $oModel->$sColumn;
            $data[$sKeyField] = $oModel->$sColumn;
        }
        return $data;
    }

    /**
     *
     * @param array $aCondition [条件数组]
     * @return builder             [查询构造器对象]
     */
    public function doWhere($aCondition = [])
    {
        is_array($aCondition) or $aCondition = [];
        $oQuery = static::where('id', '>', '0');
        foreach ($aCondition as $sColumn => $condition) {
            switch ($condition[0]) {
                case '=':
                    $oQuery = is_null($condition[1]) ? static::whereNull($sColumn) : static::where($sColumn, '=', $condition[1]);
                    break;
                case 'in':
                    $aInData = is_array($condition[1]) ? $condition[1] : explode(',', $condition[1]);
                    $oQuery = static::whereIn($sColumn, $aInData);
                    break;
                case '>':
                case '<':
                case '>=':
                case '<=':
                case 'like':
                case '<>':
                case '!=':
                    $oQuery = is_null($condition[1]) ? static::whereNotNull($sColumn) : static::where($sColumn, $condition[0], $condition[1]);
                    break;
                case 'between':
                    $aBetween = is_array($condition[1]) ? $condition[1] : implode(',', $condition[1]);
                    $oQuery = static::whereBetween($sColumn, $aBetween);
                    break;
            }
        }
        return $oQuery;
    }

    /**
     * @param null $oQuery
     * @param array $aOrderBy [排序数组]
     * @return  builder             [查询构造器对象]
     */
    public function doOrderBy($oQuery = null, $aOrderBy = [])
    {
        $aOrderBy or $aOrderBy = $this->oderColumn;
        $oQuery or $this;
        foreach ($aOrderBy as $sColumn => $type) {
            $oQuery->orderBy($sColumn, $type);
        }
        return isset($oQuery) ? $oQuery : $this;
    }

    /**
     * @param null $oQuery
     * @param array $aGroupBy [分组查询数组
     * @return BaseModel|null
     */
    public function doGroupBy($oQuery = null, $aGroupBy = [])
    {
        $aGroupBy or $this->groupByColumn;
        $oQuery or $this;
        foreach ($aGroupBy as $group) {
            $oQuery->groupBy($group);
        }
        return isset($oQuery) ? $oQuery : $this;
    }

    /**按条件查询某一条记录对象
     * @param array $aParams
     * @return mixed
     */
    public static function getObjectByParams(array $aParams = ['*']) {
        return static::getObjectCollectionByParams($aParams)->first();
    }

    /**
     * 按条件获得所有对象
     * @param array $aParams
     * @return object
     */
    public static function getObjectCollectionByParams(array $aParams = ['*']) {
        $oQuery = null;
        foreach($aParams as $key=> $aParam){
            if(isset($oQuery) && is_object($oQuery)){
                $oQuery=static::where($key,'=',$aParam);
            }else{
                $oQuery=static::where($key,'=',$aParam);
            }
        }
        return $oQuery->get();
    }

}