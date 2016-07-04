<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 16-4-9
 * Time: 下午1:53
 */

namespace App\Models;
use App\Models\User;

class SystemLogger extends BaseModel

{

    protected $table = 'yascmf_system_log';
    protected $fillable = [
        'user_id',
        'type',
        'url',
        'content',
        'operator_ip',
        'created_at'
    ];
    public static $resourceName='system_logger';
    /**
     * 定义相对关联
     */

    public function user(){

        return static::belongsTo('App\Models\User','user_id','id');
    }


    public static function writeLog($user_id,$url,$client_ip,$route,$content){

        $oUser=User::find($user_id);
        $oLogger= new self;
        $oLogger->user_id=$user_id;
        $oLogger->type=$oUser->user_type;
        $oLogger->url=$url;
        $oLogger->operator_ip=$client_ip;
        //log内容组装
        $oLogger->content=User::$userType[$oUser->user_type].'_'.$oUser->username.'_'.$route.'_'.$content;
        $oLogger->save();
    }

}