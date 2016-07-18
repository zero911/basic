<?php

namespace App\Models\Func;

use App\Models\BaseModel;

class SearchItem extends BaseModel
{

    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;

    protected $table = 'yascmf_search_item';
    protected $fillable = [
        'search_config_id',
        'model',
        'field',
        'label',
        'type',
        'default_value',
        'source',
        'div',
        'empty',
        'empty_text',
        'match_rule',
        'sequence',
    ];
    public static $resourceName = 'label';
    public static $rules = [

        'search_config_id' => 'integer',
        'model'            => 'between:1,32',
        'field'            => 'between:1,32',
        'label'            => 'between:1,32',
        'type'             => 'between:1,32',
        'default_value'    => 'between:1,32',
        'div'              => 'boolean',
        'empty'            => 'boolean',
        'match_rule'       => 'required|between:1,1024',
        'sequence'         => 'integer',
    ];
    /**
     * The array of custom error messages.
     *
     * @var array
     */
    public static $customMessages = [];

    /**
     * title field
     * @var string
     */
    public static $titleColumn = 'label';

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'search_config_id';

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'sequence' => 'asc'
    ];

    /**
     * 解析给定的数据源，以数组的形式返回，仅用于type为select时
     *
     * @param string $sSource
     * @return array
     */
    public static function explainSource($sSource){
        $aSearchFields = [];
        switch ($sSource{0}) {
            case '*':   // 与模型关联
                $sRelatedConfig = substr($sSource, 1);
                $aRelatedConfig = explode('|', $sRelatedConfig);        // 分解，数组0为模型与字段信息，数组1为条件
                $aRelatedModelInfo = explode('.', $aRelatedConfig[0]);  // 分解模型与字段信息，数组0为模型名，数组1为字段
                $sRelatedModel = Config::get('namespace-map.' . $aRelatedModelInfo[0]);
                $oRelatedModel = app()->make($sRelatedModel);             // Create Related Model
                if (isset($aRelatedModelInfo[1])) {                     // 如果指定了字段，则取此字段值的列表，注意：此时option的值与字段值相同
                    $sRelatedField = $aRelatedModelInfo[1];
                    $bGetSingleField = true;
                } else {                                        // 未指定字段，则意为options的来源为使用id为键，Model::$titleColumn字段为值的数组，此时Options的值为数字，即Model的ID
                    $sRelatedField = $sRelatedModel::$titleColumn;
                    $bGetSingleField = false;
                }
//                pr($sRelatedField);
                // pr($oRelatedModel);
                // exit;
                $aColumns = & $oRelatedModel->getColumnTypes();
//                pr($aColumns);
//                exit;
                $aInfoForFindMethod = [];
                $aConditions = [];
                if (array_key_exists('disabled', $aColumns)) {
                    $aConditions["disabled"] = ['=',false];
                }
//                pr(count($aRelatedConfig));
                if (count($aRelatedConfig) > 1) {
                    $aRelatedContidions = explode(',', $aRelatedConfig[1]);
//                    pr($aRelatedContidions);
                    foreach ($aRelatedContidions as $sCondition) {
                        list($sField, $sValue) = explode('=', $sCondition);
                        $aConditions[$sField] = ['=', $sValue == 'null' ? null : $sValue];
                    }
                }
//                pr($aConditions);
//                pr($bGetSingleField);
//                exit;
                if ($bGetSingleField) {
                    $source = $oRelatedModel->getValueListArray($sRelatedField, $aConditions);
                } else {
                    if ($sRelatedModel::$treeable && empty($aConditions)) {
                        $oRelatedModel->getTree($source,null,null,[$sRelatedModel::$titleColumn => 'asc']);
                    } else {
                        $source = $oRelatedModel->getValueListArray($sRelatedField, $aConditions, null, true);
                    }
                }
//                pr($source);
//                exit;
                break;
//                        pr($source);
            case '$':       // var mode, return the original string
                $source = trim($sSource);
                break;
            default:
                $source = explode("\n",$sSource);
                break;
        }
        return $source;
    }

    function beforeValidate(){
        if ($this->search_config_id){
            $oSearchConfig = SearchConfig::find($this->search_config_id);
//            $this->functionality_id = $oSearchConfig->functionality_id;
        }
        return parent::beforeValidate();
    }

    private static function compileCacheKey($iSearchConfigId){
        return static::getCachePrefix(true) . $iSearchConfigId;
    }

    public static function & createCache($iSearchConfigId){
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            return false;
        }
        $sKey = static::compileCacheKey($iSearchConfigId);
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        Cache::forget($sKey);
        $data = & static::getItemListFromDb($iSearchConfigId);
        Cache::forever($sKey, $data);
        return $data;
    }

    protected function afterCreate($oCreatedModel){
        static::deleteFormCache($oCreatedModel->search_config_id);
        return true;
    }

    protected function afterDelete($oDeletedModel){
        static::deleteFormCache($oDeletedModel->search_config_id);
        return true;
    }

    protected function afterSave($oSavedModel){
        if (!parent::afterSave($oSavedModel)){
            return false;
        }
        static::deleteFormCache($oSavedModel->search_config_id);
        return true;
    }

    public static function deleteFormCache($iSearchConfigId){
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            return false;
        }
        $sKey = static::compileCacheKey($iSearchConfigId);
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        return Cache::forget($sKey);
    }

    public static function & getItemList($iSearchConfigId){
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            $data = & static::getItemListFromDb($iSearchConfigId);
        }
        else{
            Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
            $sKey = static::compileCacheKey($iSearchConfigId);
            if (!$data = Cache::get($sKey)){
                $data = & static::createCache($iSearchConfigId);
            }
        }
        return $data;
    }

    private static function & getItemListFromDb($iSearchConfigId){
        $data = [];
        $aItemObj = self::where('search_config_id', '=', $iSearchConfigId)->orderBy('sequence')->get();
        foreach($aItemObj as $oSearchItem){
            $data[$oSearchItem->field] = $oSearchItem->getAttributes();
        }
        return $data;
    }

    public static function getValidTypes(){
        return static::_getArrayAttributes(__FUNCTION__);
    }

    protected function getTypeFormattedAttribute(){
        return $this->attributes['type'];
    }
}