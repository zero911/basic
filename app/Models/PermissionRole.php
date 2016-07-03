<?php
/**
 * [用户角色的权限关系模型].
 * User: ziann
 * Date: 16/3/25
 * Time: 下午8:28
 */

namespace App\Models;


class PermissionRole extends BaseModel
{
    protected $table='yascmf_permission_role';
    public $timestamps=false;
    public static $resourceName='permission_role';
    /** [获得当前角色的所有权限id]
     * @param $iRoleId
     * @return array
     */

    public static function getPermissionIdByRoleId($iRoleId){
        $aPermissionIds=[];
        $oPermissionIds=static::where('role_id','=',$iRoleId)->get(['permission_id']);

        foreach($oPermissionIds as $permissionId){
            $aPermissionIds[]=$permissionId->permission_id;
        }
        return $aPermissionIds;
    }
}