<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 16-3-28
 * Time: 下午1:49
 */

namespace App\Models;


class SystemOptions extends BaseModel
{
    protected $table='yascmf_system_options';

    protected $fillable=[
        'id','name','value'
    ];
    public static $resourceName='system_option';
}