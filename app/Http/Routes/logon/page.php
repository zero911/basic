<?php
/**
 * Created by PhpStorm.
 * User: ziann
 * Date: 16/3/27
 * Time: 上午9:30
 */

$sPrefix='page';

Route::group(['prefix'=>$sPrefix],function () use ($sPrefix){

    $sController='PageController@';

    Route::get('/',['as'=>$sPrefix.'.index','uses'=>$sController.'index']);
    Route::get('{id}/view',['as'=>$sPrefix.'.view','uses'=>$sController.'view']);
    Route::any('{id}/edit',['as'=>$sPrefix.'.edit','uses'=>$sController.'edit']);
    Route::any('create',['as'=>$sPrefix.'.create','uses'=>$sController.'create']);
    Route::get('{id}/destroy',['as'=>$sPrefix.'.destroy','uses'=>$sController.'destroy']);
});