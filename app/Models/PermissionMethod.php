<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 16-3-31
 * Time: 下午4:02
 */

namespace App\Models;


class PermissionMethod extends BaseModel
{
    protected $table = 'yascmf_permission_method';
    public $timestamps = false;
    public static $resourceName='permission_method';
}