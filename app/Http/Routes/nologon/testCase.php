<?php
use App\Models\Admin\User;
use App\Models\Admin\Role;
use App\Models\Func\Functionality;
use App\Models\Func\FunctionalityRelation;

// use Cache;

// use Config;

Route::get('testCase', ['as' => 'test-case', 'uses' => function () {


    dump(number_format('0.323233',3));
    echo number_format(intval('0.323233'*1000)/1000,3);


//    $oUser = new \App\Models\User();
//    $sDatabase = $oUser->getConnection()->getConfig('database');
//    dd($sDatabase);
//    $sSql = "select*from information_schema.columns where table_schema='$sDatabase' and table_name='yascmf_users' order by ordinal_position;";
//    $aColumns = DB::select($sSql);
//dump($aColumns);
//    $response = Curl::to($url)->withData($aPostData)->post();
//    pr($response);exit;
//    $key = md5('App\Models\Admin\User') . '_64';
//    $cache = Cache::get($key);
//    pr($cache);exit;
//    for ($i=0; $i < 10; $i++) {
//        // echo uniqid(rand(1, 100000));
//        $year_code = array('A','B','C','D','E','F','G','H','I','J');
//        $order_sn = $year_code[intval(date('Y'))-2016].
//        strtoupper(dechex(date('m'))).date('d').
//        substr(time(),-5).substr(microtime(),2,5).sprintf('%02d',rand(0,99));
//        echo $order_sn; // date('ymd').substr(time(),-5).substr(microtime(),2,5);
//        echo "</br>";
//    }

    exit;

}]);