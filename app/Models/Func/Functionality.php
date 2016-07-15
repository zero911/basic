<?php

namespace App\Models\Func;

use App\Models\BaseModel

class Functionality extends BaseModel
{

    const REALM_SYSTEM = 0;
    const REALM_ADMIN = 1;
    const REALM_USER = 2;

    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;

    protected $table = 'yascmf_functionalities';
    protected $fillable = [
        'title',
        'parent_id',
        'parent',
        'forefather_ids',
        'forefathers',
        'description',
        'controller',
        'action',
        'button_type',
        'popup_id',
        'popup_title',
        'button_onclick',
        'refresh_cycle',
        'menu',
        'need_curd',
        'need_search',
        'search_config_id',
        'realm',
        'need_log',
        'sequence',
    ];
    public static $resourceName = 'Functionality';
    public static $rules = [

        'title' => 'required|between:2,10',
        'parent_id' => 'integer',
        'description' => 'between:2,60',
        'controller' => 'required|alpha:2,40',
        'action' => 'required|alpha:2,40',
        'button_type' => 'required|integer',
        'popup_id' => 'integer',
        'popup_title' => 'required|alpha:2,40',
        'button_onclick' => 'between:5,30',
        'refresh_cycle' => 'integer',
        'menu' => 'required|boolean',
        'need_curd' => 'required|alpha:2,40',
        'need_search' => 'required|boolean',
        'search_config_id' => 'integer',
        'realm' => 'required|string',
        'need_log' => 'required|boolean',
        'sequence' => 'integer',
    ];

    public static $validTypes = [
        self::REALM_SYSTEM => 'system',
        self::REALM_ADMIN => 'admin',
        self::REALM_USER => 'user'
    ];

    public static function getValidTypes()
    {

        return static::_getArrayAttributes(__FUNCTION__);
    }

    public static function getByCA($sController, $sAction, array $aRealm=[self::REALM_ADMIN])
    {

        if (static::$cacheLevel != static::CACHE_LEVEL_NONE) {

            $sCachePrefixKey = static::getCacheKeyByCA($sController, $sAction);
            Cache::setDefautDriver(static::$cacheDrivers[static::$cacheLevel]);

            if($aAttributes=Cache::get($sCachePrefixKey)){

                $obj=new static;
                $obj=$obj->newFromBuilder($aAttributes);

            }else{

                $obj=static::getByCAFromDb($sController,$sAction,$aRealm);

                if(is_object($obj)){
                    Cache::forever($sCachePrefixKey,$obj->getAttributes());
                }else{
                    return false;
                }
            }

            if($aRealm && !in_array($obj->realm,$aRealm)){
                unset($obj);
            }
        }

        if (!isset($obj)){
            $obj = static::getByCAFromDb($sController,$sAction,$aRealm);
        }
        return $obj;
    }

    public static function getCacheKeyByCA($sController, $sAction)
    {
        return static::getCachePrefix() . '-' . $sController . '-' . $sAction;
    }

    public static function getByCAFromDb($sController,$sAction,$bNeedRealm=false){

        $aConditions=[
            'controller'=>['=',$sController],
            'action'=>['=',$sAction],
        ];
        !$bNeedRealm or $aConditions['realm']= ['in',$bNeedRealm];
        return static::doWhere($aConditions)->get()->first();
    }
}