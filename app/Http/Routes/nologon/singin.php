<?php
/**
 * Created by PhpStorm.
 * User: ziann
 * Date: 16/3/23
 * Time: 下午10:46
 */
$sPrefix = 'auth';
Route::group(['prefix' => $sPrefix], function () {

    $sController = 'AuthorityController@';
    Route::match(['get', 'post'], 'login', ['as' => 'login', 'uses' => $sController . 'login']);//登陆
    Route::get('logout', ['as' => 'admin.logout', 'uses' => $sController . 'logout']);//登出
    Route::get('visit', ['as' => 'visit', 'uses' => $sController . 'testVisitCurl']);//登出
//    Route::any('forgot', ['as' => 'forgot', 'uses' => $sController . 'forgotPwd']);//忘记密码
});