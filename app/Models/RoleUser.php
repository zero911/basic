<?php
/**
 * [用户角色关联模型]
 * User: ziann
 * Date: 16/3/25
 * Time: 下午8:24
 */

namespace App\Models;


class RoleUser extends BaseModel
{

    protected $table='yascmf_role_user';
    public $timestamps=false;
    public static $resourceName='role_user';
}