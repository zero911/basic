<?php

namespace App\Models\Func;

use App\Models\BaseModel;

class FunctionalityParam extends BaseModel
{

    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;

    protected $table = 'yascmf_functionality_param';
    protected $fillable = [

        'functionality_id',
        'name',
        'type',
        'default_value',
        'limit_when_null',
        'sequence',
    ];
    public static $resourceName = 'name';
    public static $rules = [

        'functionality_id' => 'required|integer',
        'name' => 'required|alpha:2,40',
        'type' => 'alpha:2,40',
        'limit_when_null' => 'boolean',
        'sequence' => 'numeric',
    ];

    /** [根据functionality_id获取functionality_params 加入缓存]
     * @param $iFunctionalityId int
     * @return array
     */
    public static function getParams($iFunctionalityId)
    {

        if (static::$cacheLevel == static::CACHE_LEVEL_NONE) {
            return static::getParamsFromDb($iFunctionalityId);
        }

        $sCacheKey = static::getFunctionalityParamsCacheKey($iFunctionalityId);
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);

        if (Cache::has($sCacheKey)) {
            $aParams = Cache::get($sCacheKey);
        } else {
            $aParams = static::getParamsFromDb($iFunctionalityId);
            Cache::forever($sCacheKey, $aParams);
        }
        return $aParams;
    }

    /** [根据functionality_id设置缓存key]
     * @param $iFunctionalityId int
     * @return string
     */
    public static function getFunctionalityParamsCacheKey($iFunctionalityId)
    {
        return static::getCachePrefix(true) . 'functionality-' . $iFunctionalityId;
    }

    public static function getCacheKeyByCA($sController, $sAction)
    {
        return static::getCachePrefix() . '-' . $sController . '-' . $sAction;
    }

    /** [根据functionality获取所有的functionality_param]
     * @param $iFunctionalityId
     * @return array
     */
    public static function getParamsFromDb($iFunctionalityId)
    {

        $ret = [];

        $aFields = [
            'functionality_id',
            'name',
            'type',
            'default_value',
            'limit_when_null',
            'sequence',
        ];

        $aFunctionalityParams = static::where('functionality_id', $iFunctionalityId)->orderBy('sequence', 'asc')->get($aFields);

        foreach ($aFunctionalityParams as $oFunctionalityParam) {
            $ret[$oFunctionalityParam->name] = $oFunctionalityParam->getAttributes();
        }

        return $ret;
    }
}