<?php

$sPrefix = 'console';

Route::group(['prefix' => $sPrefix], function () use ($sPrefix) {

    $sController = 'AdminController@';

    Route::get('cache', ['as' => $sPrefix . '.cache', 'uses' => $sController . 'resetCache']);
});