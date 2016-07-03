<?php
/**
<<<<<<< HEAD
 * Created by PhpStorm.
=======
 * meta模型,目前暂对catetory支持
>>>>>>> eeeb76276877854906f55781d3cebf81c9eff7cd
 * User: ziann
 * Date: 16/3/26
 * Time: 上午12:13
 */

namespace App\Models;


class Metas extends BaseModel
{
    protected $table='yascmf_metas';
    protected $fillable=[
        'name','thumb','type','slug','description','count','sort',
    ];
    public $timestamps=false;
    public static $resourceName='meta';
    public static $rules=[
        'name'=>'required|between:2,10',
        'description'=>'required|between:2,20',
        'slug'=>'required|alpha_dash|between:2,10',
    ];

    /** [得到所有分类名称]
     * @return array
     */
    public static function getAllCategories(){
        return static::where('type','=','category')->get();
    }

    /**[分类统计+1]
     * @param $id int
     * @param $type string
     */
    public static function setCountById($id,$type){
        $_objct=static::where('type','=',$type)->where('id','=',$id)->first();
        $_objct->count=$_objct->count+1;
        $_objct->save();
        exit;
    }
}