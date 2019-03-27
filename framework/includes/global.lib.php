<?php
/**
 * 缓存服务器
 * @param  [type] $cacheConfig [description]
 * @return [type]              [description]
 */
function &cache_server($cacheConfig = CACHE_CONFIG)
{
    import('cache.lib');
    static $CS = array();
    $dkey = msubstr(md5($cacheConfig), 8, 16);
    if (!isset($CS[$dkey])) {
        $cacheConfig = parse_url($cacheConfig);
        $cacheConfig['path'] = str_replace('/', '', $cacheConfig['path']);
        $cacheName = $cacheConfig['scheme'] . "Server";
        $cacheServer = new $cacheName($cacheConfig);
        
        false && $cacheServer = new RedisServer();
        $CS[$dkey] = $cacheServer;
    }
    return $CS[$dkey];
}

/**
 *    获取环境变量
 *
 *    @author 刘阳(alexdany@126.com)
 *    @param     string $key
 *    @param     mixed  $val
 *    @return    mixed
 */
function &env($key, $val = null)
{
    !isset($GLOBALS['EC_ENV']) && $GLOBALS['EC_ENV'] = array();
    $vkey = $key ? strtokey("{$key}", '$GLOBALS[\'EC_ENV\']') : '$GLOBALS[\'EC_ENV\']';
    if ($val === null) {
        /* 返回该指定环境变量 */
        $v = eval('return isset(' . $vkey . ') ? ' . $vkey . ' : null;');

        return $v;
    } else {
        /* 设置指定环境变量 */
        eval($vkey . ' = $val;');
        return $val;
    }
}
