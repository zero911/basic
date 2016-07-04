<?php
/**
 * Created by PhpStorm.
 * User: ziann
 * Date: 16/3/26
 * Time: 下午6:26
 */

namespace App\Models;


class Flags extends BaseModel
{
    protected $table='yascmf_flags';
    public static $resourceName='flag';

    /**
     *  [获取所有标记对象]
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getFlags(){
        return static::all();
    }

}