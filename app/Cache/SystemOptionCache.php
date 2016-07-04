<?php
/**
 * 系统配置缓存设置类
 * User: zero
 * Date: 16-3-28
 * Time: 下午2:37
 */

namespace App\Cache;


use App\Models\SystemOptions;
use Cache;

class SystemOptionCache
{

    public static function cacheSetting()
    {

        $system_option = SystemOptions::all();

        foreach ($system_option as $option) {
            if (Cache::get('cache.driver') === 'memcached') {

                Cache::tags('syscfg', 'static')->remember($option->name, $option->value);
            } else {
                Cache::forever($option->name, $option->value);
            }
        }
    }

    public static function unCacheSetting()
    {

        if (Cache::get('cache.driver') === 'memcached') {

            Cache::tags('syscfg', 'static')->flush();
        } else {
            Cache::forget('syscfg');
        }
    }
}