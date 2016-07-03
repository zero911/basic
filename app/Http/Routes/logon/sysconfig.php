<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 16-3-28
 * Time: 下午2:01
 */

$sPrefix='syscfg';

Route::group(['prefix'=>$sPrefix],function() use ($sPrefix){

    $sController = 'SystemOptionsController@';

    Route::get('/',['as'=>$sPrefix .'.index','uses'=>$sController.'index']);
    Route::post('edit',['as'=>$sPrefix .'.edit','uses'=>$sController.'edit']);
});