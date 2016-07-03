<?php
/**
 * Created by PhpStorm.
 * User: ziann
 * Date: 16/3/25
 * Time: 下午11:51
 */

namespace App\Models;


class Articles extends BaseModel
{
    protected $table = 'yascmf_contents';
    protected $fillable = [
        'flag',
        'title',
        'category_id',
        'slug',
        'updated_at',
        'type',
        'content',
        'thumb',
        'outer_link',
        'is_top',
    ];

    public static $rules=[
        'title'=>'required|between:1,80',
        'content'=>'required|min:10',
        'is_top'=>'boolean',
        'category_id'=>'numeric'
    ];

    public static $resourceName='article';
}