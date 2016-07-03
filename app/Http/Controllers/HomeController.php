<?php
/**
 * Created by PhpStorm.
 * User: ziann
 * Date: 16/3/23
 * Time: 下午10:21
 */

namespace App\Http\Controllers;

use App\Http\Controllers\AdminBaseController;
use App\Models\User;
use Session;

class HomeController extends AdminBaseController
{
    /**
     * 后台首页处理函数
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getHome()
    {
        $iUserId = Session::get('admin_user_id');
        //得到菜单
        $menus=User::getRights($iUserId,true);
        if(!is_array($menus)){
            return view('back.view',['menus'=>null,'noRoleMsg' => '当前用户无角色信息，请联系超级管理员']);
        }
        return view('back.index', ['menus' => $menus]);
    }
}