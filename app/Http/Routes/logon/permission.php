<?php
/**
 * Created by PhpStorm.
 * User: ziann
 * Date: 16/3/26
 * Time: 上午1:41
 */

$sPrefix = 'permission';
Route::group(['prefix' => $sPrefix], function () use ($sPrefix) {

    $sController = 'PermissionController@';

    Route::get('{id}/view', ['as' => $sPrefix . '.view', 'uses' => $sController . 'view']);
    Route::get('destroy/{id}', ['as' => $sPrefix . '.destroy', 'uses' => $sController . 'destroy']);
    Route::get('/', ['as' => $sPrefix . '.index', 'uses' => $sController . 'index']);
    Route::any('{id}/edit', ['as' => $sPrefix . '.edit', 'uses' => $sController . 'edit']);
    Route::any('create', ['as' => $sPrefix . '.create', 'uses' => $sController . 'create']);
});