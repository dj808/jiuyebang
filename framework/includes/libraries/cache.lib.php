<?php
define('CACHE_DIR_NUM' , 20000); // 缓存目录数量，根据预期缓存文件数调整，开根号即可

/**
 *    基础缓存类接口
 * @author    宗全珍
 */
class CacheServer {
    public $_options = null;

    public function __construct($options = null) {
        $this->_options = $options;
    }

    /**
     *    获取缓存的数据
     * @author    宗全珍
     * @param     string $key
     * @return    mixed
     */
    public function get($key) {
    }

    /**
     *    设置缓存
     *
     * @author    宗全珍
     * @param     string $key
     * @param     mixed  $value
     * @param     int    $ttl 缓存时间
     * @return    bool
     */
    public function set($key , $value , $ttl = 0) {
    }

    /**
     *    清空缓存
     *
     * @author    宗全珍
     * @return    bool
     */
    public function clear() {
    }

    /**
     *    删除一个缓存
     * @author    宗全珍
     * @param     string $key
     * @return    bool
     */
    public function delete($key) {
    }
}

/**
 *    普通PHP文件缓存
 *
 * @author    宗全珍
 * @usage     none
 */
class PhpCacheServer extends CacheServer {
    /* 缓存目录 */
    public $_cache_dir = './';

    public function set($key , $value , $ttl = 0) {
        if (!$key) {
            return false;
        }
        $cache_file = $this->_get_cache_path($key);
        $cache_data = "<?php\r\n/**\r\n *  @Created By zongqz PhpCacheServer\r\n *  @Time:" . date('Y-m-d H:i:s') . "\r\n */";
        $cache_data .= $this->_get_expire_condition(intval($ttl));
        $cache_data .= "\r\nreturn " . var_export($value , true) . ";\r\n";
        $cache_data .= "\r\n?>";

        return file_put_contents($cache_file , $cache_data , LOCK_EX);
    }

    public function &get($key) {
        $cache_file = $this->_get_cache_path($key);
        if (!is_file($cache_file)) {
            return false;
        }
        $data = include $cache_file;
        return $data;
    }

    public function clear() {
        $this->set_cache_dir(TEMP_PATH . "/caches");
        ecm_rmdir($this->_cache_dir , 0);
        return;
    }

    public function delete($key) {
        $cache_file = $this->_get_cache_path($key);
        return @unlink($cache_file);
    }

    public function set_cache_dir($path) {
        $this->_cache_dir = $path;
    }

    public function _get_expire_condition($ttl = 0) {
        if (!$ttl) {
            $ttl = CACHE_TIME;
        }
        return "\r\n\r\n" . 'if(filemtime(__FILE__) + ' . $ttl . ' < time())return false;' . "\r\n";
    }

    public function _get_cache_path($key) {
        $dir = str_pad(abs(crc32($key)) % CACHE_DIR_NUM , 4 , '0' , STR_PAD_LEFT);
        ecm_mkdir($this->_cache_dir . '/' . $dir);
        return $this->_cache_dir . '/' . $dir . '/' . $this->_get_file_name($key);
    }

    public function _get_file_name($key) {
        return md5($key) . '.cache.php';
    }

    public function __destruct() {
    }
}

/**
 *    Memcached 缓存类
 * @author    宗全珍
 * @usage     none
 */
class MemcacheServer extends CacheServer {
    public $_memcache = null;

    public function __construct($options) {
        $this->MemcacheServer($options);
    }

    public function MemcacheServer($options) {
        parent::__construct($options);
        /* 连接到缓存服务器 */
        $this->connect($this->_options);
    }

    /**
     *    连接到缓存服务器
     * @author    宗全珍
     * @param     array $options
     * @return    bool
     */
    public function connect($options) {
        if (empty($options)) {
            return false;
        }
        $this->_memcache = new Memcache;
        return $this->_memcache->connect($options['host'] , $options['port']);
    }

    /**
     *    写入缓存
     * @author    宗全珍
     * @param    string $key   要设置值的key
     * @param     ...... $value 要存储的值，字符串和数值直接存储，其他类型序列化后存储
     * @param    int    $ttl   当前写入缓存的数据的失效时间。如果此值设置为0表明此数据永不过期。
     *                         你可以设置一个UNIX时间戳或 以秒为单位的整数（从当前算起的时间差）来说明此数据的过期时间，
     *                         但是在后一种设置方式中，不能超过 2592000秒（30天）
     * @return    void
     */
    public function set($key , $value , $ttl = null) {
        return $this->_memcache->set($key , $value , MEMCACHE_COMPRESSED , $ttl);
    }

    /**
     *    获取缓存
     * @author    宗全珍
     * @param     string $key
     * @return    mixed
     */
    public function &get($key) {
        $info = $this->_memcache->get($key);
        return $info;
    }

    /**
     *    清空缓存
     * @author    宗全珍
     * @return    bool
     */
    public function clear() {
        return $this->_memcache->flush();
    }

    public function delete($key) {
        return $this->_memcache->delete($key);
    }

    public function __destruct() {
        //echo "CloseMemcache";
        $this->_memcache->close();
    }

}

/**
 * Redis缓存类
 * @author 刘小祥
 */
class RedisServer extends CacheServer {

    public $server , $compressed;

    public function __construct($options) {
        parent::__construct($options);
        $this->server = new Redis();
        $rs = $this->server->connect($options['host'] , $options['port']);

        if ($options['pass'])
            $rs = $this->server->auth(trim($options['pass']));

        $cacheId = intval($options['path']);
        $rs = $this->server->select($cacheId);

        $this->compressed = 1;
    }

    public function set($key , $value , $ttl = 0) {
        if ($value === false) {
            return false;
        }

        $value = json_encode($value);
        if ($this->compressed) {
            $value = gzcompress($value);
        }

        $isOk = $this->server->set($key , $value );

        return $isOk;
    }

    public function get($key) {
        $result = $this->server->get($key);
        if ($result === false) {
            return false;
        }
        if ($this->compressed) {
            $result = gzuncompress($result);
        }

        $value = json_decode($result , 1);
        if ($value === false) {
            return $result;
        }
        return $value;
    }

    public function delete($key , $delay = 0) {
        if ($delay == 0) {
            return $this->server->delete($key);
        }
        else {
            //return $this->server->setTimeout($key, $delay);
            return $this->server->delete($key);
        }
    }

    public function __destruct() {
        $this->server->close();
    }
}
