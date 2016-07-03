<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 16-3-24
 * Time: 下午3:37
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Role;
use App\Models;


class User extends BaseModel implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;
//    use EntrustUserTrait;
    use SoftDeletes;
    protected $table = 'yascmf_users';
//    public $timestamps=false;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'id',
        'username',
        'nickname',
        'email',
        'realname',
        'password',
        'address',
        'phone',
        'is_lock',
        'user_type',
    ];
    public static $resourceName='user';

    public static $rules = [
        'username' => 'required|alpha_num|between:5,10',
        'password' => 'required|confirmed|alpha_dash|between:6,16',
        'email' => 'required|email',
        'realname' => 'required|between:2,4',
        'phone' => 'numeric',
    ];

    //系统的user类型常量
    const USER_TYPE_VISITOR = 'vistor';
    const USER_TYPE_MANAGER = 'manager';
    const USER_TYPE_CUSTOMER = 'customer';

    public static $userType = [
        self::USER_TYPE_CUSTOMER => '投资型用户',
        self::USER_TYPE_MANAGER => '系统管理员',
        self::USER_TYPE_VISITOR => '游客'
    ];

    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'yascmf_role_user', 'user_id', 'role_id');
    }

    /**
     *  [通过用户id得到用户的角色]
     * @param $user_id int [用户id]
     * @return array [返回用户角色id数组]
     */
    public static function getRoles($user_id)
    {
        $aMyRoles = [];
        $oRoles = static::find($user_id)->roles()->get();

        foreach ($oRoles as $role) {
            //排除用户是否新用户五角色的可能
            if (is_object($role->pivot) && $role->pivot) {
                $aMyRoles[] = $role->pivot->role_id;
            }
        }
        return $aMyRoles;
    }

    public static function getUserForName($username)
    {
        return static::where('username', '=', $username)->first();
    }

    public static function getUserForType($type)
    {
        return static::where('user_type', '=', $type)->where('is_lock', '=', 0)->get();
    }

    /** [更新和创建用户组建函数,目前暂未对会员账户处理]
     * @param $oModel  object
     * @param $aInputs array
     * @param string $user_type string   类型分为manager|visitor|customer
     * @param $is_created boolean  决定是创建还是修改user
     * @return mixed object
     */
    public function compileContent($oModel, $aInputs, $user_type = 'manager', $is_created = true)
    {

        $oUser = $this->getUserForName($aInputs['username']);
        if (is_object($oUser) && $is_created) {
            return null;
        }

        if ($user_type === 'manager') {
            $oModel->username = $aInputs['username'];
            $oModel->realname = $aInputs['realname'];
        } elseif ($user_type === 'visitor') {

        } else {

        }
        if (array_key_exists('phone', $aInputs)) {
            $oModel->phone = $aInputs['phone'];
        }
        if (array_key_exists('address', $aInputs)) {
            $oModel->address = $aInputs['address'];
        }
        if (array_key_exists('is_lock', $aInputs)) {
            $oModel->is_lock = $aInputs['is_lock'];
        }
        if (array_key_exists('nickname', $aInputs)) {
            $oModel->nickname = $aInputs['nickname'];
        }
        if (array_key_exists('password', $aInputs)) {
            $oModel->password = bcrypt($aInputs['password']);
        }
        if (array_key_exists('email', $aInputs)) {
            $oModel->email = $aInputs['email'];
        }
        $oModel->user_type = $user_type;
        return $oModel;
    }

    /** 得到用户权限列表
     * @param $user_id int 用户id
     * @param $is_menu boolean true|false 是否菜单权限
     * @return null|array
     */
    public static function getRights($user_id, $is_menu)
    {
        $aRoleIds = static::getRoles($user_id);

        if (count($aRoleIds) < 1) {
            return null;
        }
        //得到用户的所有模块id
        $aMethodIds = Role::getMethods($aRoleIds, $is_menu);
        //得到所有当前用户的所有模块
        $aMethods = Methods::getTrees($aMethodIds, $is_menu);
        return $aMethods;
    }
}