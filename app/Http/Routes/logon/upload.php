<?php

$sPrefix='upload';

Route::group(['prefix'=>$sPrefix],function()use($sPrefix){

    $sController='AdminController@';

    Route::get('/',['as'=>$sPrefix.'.upload','uses'=>$sController.'getUpload']);
    Route::post('/',['as'=>$sPrefix.'.store','uses'=>$sController.'postUpload']);
});