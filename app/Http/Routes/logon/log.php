<?php
/**
 * Created by PhpStorm.
 * User: ziann
 * Date: 16/3/26
 * Time: 上午1:41
 */

$sPrefix = 'log';
Route::group(['prefix' => $sPrefix], function () use ($sPrefix) {

    $sController = 'SystemLoggerController@';

    Route::get('{id}/view', ['as' => $sPrefix . '.view', 'uses' => $sController . 'view']);
    Route::get('/', ['as' => $sPrefix . '.index', 'uses' => $sController . 'index']);
    Route::get('exportExcel', ['as' => $sPrefix . '.exportExcel', 'uses' => $sController . 'exportExcel']);
});