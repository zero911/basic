<?php
/**
 * Created by PhpStorm.
 * User: ziann
 * Date: 16/3/28
 * Time: 上午10:09
 */

namespace App\Cache;

use Cache;

class SettingCache
{
    /** 设置缓存
     * @param $type_name string 缓存字段
     * @param string $format string object|array
     * @return bool
     */
    public static function cacheSetting($type_name, $format = 'object')
    {

        $system_type = SystemType::where('name', '=', e($type_name))->first();
        if (!is_object($system_type)) {
            return false;
        }
        $type_id = $system_type->id;
        $systems = Systems::where('type_id', '=', $type_id)->where('status', '=', 1)->get();
        if ($format === 'array') {
            $set = [];
            foreach ($systems as $system) {
                $set[$system->name] = $system->value;
            }
        } else {
            $set = $systems;
        }

        if (Cache::get('cache.driver') === 'memcached') {
            Cache::tags('setting', $type_name)->remember($type_name, 60, function () use ($type_id, $set) {
                return $set;
            });
        } else {
            Cache::remember($type_name, 60, function () use ($type_id, $set) {
                return $set;
            });
        }
        return true;
    }

    /**[清空缓存]
     * @param string $type_name  string  ''清空所有缓存,不为空则指定清空
     */
    public static function unCacheSetting($type_name = '')
    {

        if ($type_name === '') {
            if (Cache::get('cache.driver') === 'memcached') {
                Cache::tags('setting')->flush();
            } else {
                $system_types = SystemType::lists('name');
                foreach ($system_types as $st) {
                    static::unCacheSetting($st);
                }
            }
        } else {
            if (Cache::get('cache.driver') === 'memcached') {
                Cache::tags('setting',$type_name)->flush();
            }
            Cache::forget($type_name);
        }
    }
}