<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 16-3-28
 * Time: 下午1:24
 */

namespace App\Models;


class Settings extends BaseModel
{
    protected $table = 'yascmf_settings';
    public $timestamps = false;
    public static $resourceName='setting';
    public static $rules = [
        'name' => 'required|alpha_dash|between:2,32',
        'value' => 'required|min:5',
        'type_id' => 'numeric',
        'status' => 'boolean',
    ];

    public function type()
    {
        return $this->belongsTo('App\Models\SettingType', 'type_id');
    }
}