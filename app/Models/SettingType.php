<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 16-3-28
 * Time: 下午1:26
 */

namespace App\Models;


class SettingType extends BaseModel
{
    protected $table = 'yascmf_setting_type';
    public $timestamps = false;//取消自动更新时间
    public static $resourceName='setting_type';
    public static $rules = [
        'name' => 'required|between:2,30',
        'value' => 'required|between:2,200',
        'sort' => 'numeric',
    ];

}