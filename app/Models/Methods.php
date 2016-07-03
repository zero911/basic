<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 16-3-31
 * Time: 下午3:54
 */

namespace App\Models;


class Methods extends BaseModel
{
    protected $table = 'yascmf_method';
    protected $fillable = [
        'method_code',
        'name',
        'url',
        'is_actived',
    ];

    public static $rules = [
        'name' => 'required|between:2,15',
        'pid' => 'required|numeric',
        'is_actived' => 'boolean',
        'method_code' => 'alpha_dash',
    ];
    public static $resourceName='method';
    /**[获取所有顶级模块]
     * @return mixed
     */
    public static function getTopMethods()
    {
        return static::where('pid', '=', 0)->where('is_actived', '=', 1)->get();
    }

    /** [获取指定顶级模块的子集模块对象]
     * @param $pid
     * @return mixed
     */
    public function getKids($pid)
    {
        $oKids = static::where('pid', '=', $pid)->get(['id', 'name']);
        return $oKids;
    }

    /** [通过名字获取该模块对象]
     * @param $name
     * @return mixed
     */
    public static function getMethodByName($name)
    {
        return static::where('name', '=', $name)->first();
    }

    public static function getMethods()
    {
        return static::get(['id', 'name']);
    }

    public function permissions()
    {
        return static::belongsToMany('App\Models\Permission', 'yascmf_permission_method',
            'method_id', 'permission_id');
    }

    /** [保存和更新数据组建]
     * @param $oModel object
     * @param $aInputs array
     * @param bool $is_create boolean true:create
     * @return array
     */
    public function saveContent($oModel, $aInputs, $is_create = true)
    {
        if ($is_create && static::getMethodByName($aInputs['name'])) {
            return null;
        }
        $oModel->name = e($aInputs['name']);
        $oModel->pid = e($aInputs['pid']);
        $oModel->is_actived = e($aInputs['is_actived']);
        !array_key_exists('url', $aInputs) or $oModel->url = e($aInputs['url']);
        !array_key_exists('method_code', $aInputs) or $oModel->method_code = $aInputs['method_code'];
        return $oModel;
    }

    /** [按照分类得到所有id]
     * @param $aMethodId array
     * @param $is_menu boolean true|false
     * @return string
     */
    public static function getTrees($aMethodId,$is_menu)
    {
        $tmp = [];
        $oTopMethods = static::getTopMethods()->toArray();
        $oMethods = static::where('is_actived', '=', 1)->whereIn('id',$aMethodId)->get()->toArray();
        foreach ($oTopMethods as $top) {
            foreach ($oMethods as $method) {
                if ($method['pid'] == $top['id']) {
                    if(!$is_menu){
                        $top['kids'][] = $method;
                    }else{//如果菜单权限则整合url
                        $newMethod=[
                            'id'=>$method['id'],
                            'method_code'=>$method['method_code'],
                            'name'=>$method['name'],
                            'url'=>explode(',',$method['url'])[0],
                            'is_actived'=>$method['is_actived'],
                            'pid'=>$method['pid'],
                            'created_at'=>$method['created_at'],
                            'updated_at'=>$method['updated_at'],
                        ];
                        $top['kids'][] = $newMethod;
                    }

                }
            }
            $tmp[] = $top;
        }
        return $tmp;
    }

    /**
     *  [得到所有的模块和对应的权限]
     * @return array
     */
    public static function methodPermissions()
    {

        $oMethods = static::where('is_actived', '=', 1)->get()->toArray();
//        pr($oMethods);exit;
        $aResult = [];
        foreach ($oMethods as $method) {

            if ($method['pid'] == 0) {
                //一级菜单只拥有访问权限
                $aResult[$method['id']] = $method;
                $aResult[$method['id']]['permissions'] = Permission::getPermissionByType();
            } else {
                if ($method['id'] == 10) {
                    //角色拥有设置权限、crud、访问权限
                    $aResult[$method['id']] = $method;
                    $aResult[$method['id']]['permissions'] = Permission::getPermissionByType(
                        [Permission::PERMISSION_MENU_TYPE, Permission::PERMISSION_PAGE_TYPE, Permission::PERMISSION_PAGE_SET_TYPE]);
                } else {
                    //其他拥有crud和访问权限
                    $aResult[$method['id']] = $method;
                    $aResult[$method['id']]['permissions'] = Permission::getPermissionByType([Permission::PERMISSION_MENU_TYPE, Permission::PERMISSION_PAGE_TYPE]);
                }
            }
        }
        return $aResult;
    }

    /** [得到当前角色的模块权限]
     * @param $role_id
     * @return array
     */
    public static function roleHasMethodPermissions($aIds)
    {

        $objs = PermissionMethod::whereIn('id', $aIds)->get();
        $aResult = [];
//        pr($obj);exit;
        foreach ($objs as $key => $obj) {

            if (isset($aResult[$obj->method_id])) {
                $aResult[$obj->method_id]['permissions'][$obj->permission_id] = [
                    'id' => $obj->permission_id,
                    'display_name' => $obj->permission_name,
                    'has_permission' => 1,
                ];
            } else {
                $aResult[$obj->method_id] = [
                    'name' => $obj->method_name,
                    'id' => $obj->method_id,
                    'permissions' => [],
                ];
            }

        }
        return $aResult;
    }
}

