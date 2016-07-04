<?php

namespace App\Models\Func;


class Functionality extends BaseModel
{
    protected $table='yascmf_functionalities';
    protected $fillable=[
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
    public static $resourceName='Functionality';
    public static $rules=[

        'title'=>'required|between:2,10',
        'parent_id'=>'integer',
        'description'=>'between:2,60',
        'controller'=>'required|alpha:2,40',
        'action'=>'required|alpha:2,40',
        'button_type'=>'required|integer',
        'popup_id'=>'integer',
        'popup_title'=>'required|alpha:2,40',
        'button_onclick'=>'between:5,30',
        'refresh_cycle'=>'integer',
        'menu'=>'required|boolean',
        'need_curd'=>'required|alpha:2,40',
        'need_search'=>'required|boolean',
        'search_config_id'=>'integer',
        'realm'=>'required|string',
        'need_log'=>'required|boolean',
        'sequence'=>'integer',
    ];

}