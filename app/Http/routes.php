<?php

/*
|--------------------------------------------------------------------------
| 登录路由
|--------------------------------------------------------------------------
*/

loadRoutes(Config::get('routes.nologon'));

/*
|--------------------------------------------------------------------------
| 登录后的路由
|--------------------------------------------------------------------------
*/
Route::group(['middleware'=>'auth'],function(){
    #首页
    $sController='HomeController@';
    Route::get('/',['as'=>'admin.home','uses'=>$sController.'getHome']);
    loadRoutes(Config::get('routes.logon'));
});


#定义加载路由的函数
function loadRoutes($sPath){

    $aRoutes=glob($sPath.'*.php');
    foreach($aRoutes as $route){
        include ($route);
    }
    unset($aRoutes);
}


#perimission http://www.cnblogs.com/yjf512/p/4516435.html