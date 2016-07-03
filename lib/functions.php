<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 16-3-22
 * Time: 下午7:09
 */


/**
 * 样式别名加载（支持批量加载）
 * @param  string|array $aliases 配置文件中的别名
 * @param  array $attributes 标签中需要加入的其它参数的数组
 * @return string
 */
function style($aliases, $attributes = array(), $interim = '')
{
    if (is_array($aliases)) {
        foreach ($aliases as $key => $value) {
            $interim .= (is_int($key)) ? style($value, $attributes, $interim) : style($key, $value, $interim);
        }
        return $interim;
    }
    $cssAliases = app('config')->get('extend.webAssets.cssAliases');
    $url = isset($cssAliases[$aliases]) ? $cssAliases[$aliases] : $aliases;
//    pr(app('html')->style($url,$attributes));exit;
    return app('html')->style($url, $attributes);
}

/**
 * 脚本别名加载（支持批量加载）
 * @param  string|array $aliases 配置文件中的别名
 * @param  array $attributes 标签中需要加入的其它参数的数组
 * @return string
 */
function script($aliases, $attributes = array(), $interim = '')
{
    if (is_array($aliases)) {
        foreach ($aliases as $key => $value) {
            $interim .= (is_int($key)) ? script($value, $attributes, $interim) : script($key, $value, $interim);
        }
        return $interim;
    }
    $jsAliases = app('config')->get('extend.webAssets.jsAliases');
    $url = isset($jsAliases[$aliases]) ? $jsAliases[$aliases] : $aliases;
    return app('html')->script($url, $attributes);
}

/**
 * [格式话输出函数,用于调试]
 * @param $val object/array/basic type
 */
function pr($val)
{
    $bCli = php_sapi_name() == 'cli';
    $prefix = $bCli ? "\n" : '<pre>';
    $suffix = $bCli ? "\n" : '</pre>';
    if (app('config')->get('app.debug')) {
        echo $prefix;
        print_r($val);
        echo $suffix;
    }
}

function trimArray($arrayData)
{
    $result = [];
    foreach ($arrayData as $key => $data) {
        if (is_array($data)) {
            $result[$key] = trimArray($data);
        } else {
            $result[$key] = trim($data);
        }
    }
    return $result;
}

/**
 * [翻译]
 * @param string $key
 * @param array $replace
 * @param int $uc_type 1: 首字母大写； 2：全部单词首字母大写；3：先将slug格式转换为自然语言格式，再全部单词首字母大写
 * @param string $locale 语言代码
 * @return string
 */
function __($key, $replace = array(), $uc_type = 1, $locale = null)
{
//    $pre = 'transfer.';
    !empty($replace) or $replace = [];
    $aKeyParts = explode('.', $key);
    if (count($aKeyParts) > 1) {
        list($sFile, $sKey) = $aKeyParts;
    } else {
        $sFile = '_basic';
        $sKey = $aKeyParts[0];
        $key = $sFile . '.' . $sKey;
    }
    $key = strtolower($key);
    $str = Lang::get($key, $replace, $locale);
    $str != $key or $str = String::humenlize($sKey);
    if ($uc_type > 0) {
        switch ($uc_type) {
            case 1:
                $str = ucfirst($str);
                break;
            case 2:
                $str = ucwords($str);
                break;
            case 3:
                $str = String::humenlize($str);
                $str = ucwords($str);
        }
//        $function = $uc_type == 1 ? 'ucfirst' : 'ucwords';
//        $str = $function($str);
    }
//    $str = Str::slug($str);
    return $str;
}

/** [文章属性归属标记,两种情况:1,单个属性 2,多个属性则分拆]
 * @param $flags string 文章的所有标记属性,可能多个可能一个(热门,滚动,其他)
 * @param $flag string 当前的标记属性
 * @return bool
 */
function check_string($flags, $flag)
{
    $bResult = false;
    if (!strpos($flags, ',')) {
        $bResult = $flags == $flag ? true : false;
        return $bResult;
    }

    $aFlags = explode(',', $flags);
    foreach ($aFlags as $value) {
        if ($flag == $value) {
            $bResult = true;
            break;
        } else {
            continue;
        }
    }
    return $bResult;
}

if (!function_exists('cur_nav')) {
    /**
     * 根据路由$route处理当前导航URL，用于匹配导航高亮
     * $route当前必须满足 三段以上点分 诸如 route('admin.article.index')
     *
     * @param string $route 点分式路由别名
     * @return string 返回经过处理之后路径
     */
    function cur_nav($route = '')
    {
        //explode切分法
        $routeArray = explode('.', $route);
        if ((is_array($routeArray)) && (count($routeArray) >= 2)) {
            $route1 = $routeArray[0] . '.' . $routeArray[1] . '.index';
            $route2 = $routeArray[0] . '.' . $routeArray[1];
            if (Route::getRoutes()->hasNamedRoute($route1)) {  //优先判断是否存在尾缀名为'.index'的路由
                return route($route1);
            } else {
                return route($route2);
            }
        } else {
            return route($route);
        }
    }
}

/** 后台二级菜单名称友好处理   一级菜单名/二级菜单名 ==》 二级菜单名
 * @param $sKidMenuName
 * @return mixed
 */
function friendsKidMenus($sKidMenuName)
{

    $aTmp = explode('/', $sKidMenuName);

    return $aTmp[1];
}
