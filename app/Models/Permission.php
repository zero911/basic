<?php
/**
 * [权限模型].
 * User: ziann
 * Date: 16/3/25
 * Time: 下午8:35
 */

namespace App\Models;


class Permission extends BaseModel
{

    protected $table = 'yascmf_permissions';
    public static $resourceName='permission';

    protected $fillable=[
      'name','display_name','description','type',
    ];

    const PERMISSION_MENU_TYPE = 1;//菜单权限
    const PERMISSION_PAGE_TYPE = 2;//页面行级权限,比如页面crud
    const PERMISSION_PAGE_SET_TYPE = 3;//页面行级设置权限,单独拿出

    public static $permissionType = [
        self::PERMISSION_MENU_TYPE => 'menu_permission',
        self::PERMISSION_PAGE_TYPE => 'page_permission',
        self::PERMISSION_PAGE_SET_TYPE => 'page_set_permission',
    ];

    public static $rules = [
        'name' => 'alpha_dash|between:3,60',
        'display_name' => 'required|between:2,20',
        'type' => 'required|numeric',
    ];

    public function methods()
    {
        return static::belongsToMany('App\Models\Methods', 'yascmf_permission_method' ,
            'method_id' , 'permission_id');
    }

    /** [得到角色的所有权限]
     * @param $sPermissionIds string
     * @return object
     */
    public static function getPermissionByPermissionIds($sPermissionIds)
    {
        return static::whereIn('id', $sPermissionIds)->get();
    }
    /** [按类别获取权限]
     * @param int $type
     * @return mixed
     */
    public static function getPermissionByType($type = self::PERMISSION_MENU_TYPE)
    {
        if (is_array($type)) {
            return static::whereIn('type', $type)->orderBy('type','asc')->get()->toArray();
        }
        return static::where('type', '=', $type)->orderBy('type','asc')->get()->toArray();
    }
}