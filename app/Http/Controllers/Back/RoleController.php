<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 16-3-30
 * Time: 下午4:24
 */

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;
use Request;
use Input;
use App\Models\SystemLogger;
use Session;

class RoleController extends AdminBaseController
{
    protected $modelName = 'App\Models\Role';
    protected $customPath = 'back.role';
    protected $customView = [
        'index', 'edit', 'create',
    ];
    protected $routeName = 'role';

}