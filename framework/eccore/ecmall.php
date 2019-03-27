<?php
/**
 * 系统框架核心文件，包含最基础的类与函数
 * 未经zongqz允许，该文件任何人不得更改
 * @date 2014-03-01
 */
/*---------------------以下是系统常量-----------------------*/
/* 记录程序启动时间 */
header("Content-Type:text/html;charset=utf-8");
define('START_TIME', ecm_microtime());

/* 判断请求方式 */
define('IS_POST', (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'));
/* 判断ajax*/
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
define('NOW_TIME', $_SERVER['REQUEST_TIME']);

define('API_URL', $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST']);

define('IN_ECM', true);

/* 定义PHP_SELF常量 */
define('PHP_SELF', htmlentities(isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']));

/* 当前程序版本 */
define('VERSION', '1.0');

/* 当前程序Release */
define('RELEASE', '201508014');
define('PERPAGE', 20);
define('APP_PERPAGE', 10);
/*当前服务器魔术引号功能状态*/
define('MAGIC_QUOTES', true);
ob_implicit_flush(true);
date_default_timezone_set('PRC');

/*---------------------以下是PHP在不同版本，不同服务器上的兼容处理-----------------------*/

/* 在部分IIS上会没有REQUEST_URI变量 */
$query_string = isset($_SERVER['argv'][0]) ? $_SERVER['argv'][0] : $_SERVER['QUERY_STRING'];
if (!isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = PHP_SELF . '?' . $query_string;
} else {
    if (strpos($_SERVER['REQUEST_URI'], '?') === false && $query_string) {
        $_SERVER['REQUEST_URI'] .= '?' . $query_string;
    }
}
/*---------------------以下是系统底层基础类及工具-----------------------*/
class ECMall
{
    /* 启动 */
    public static function startup($config = array())
    {
        // 自动加载函数
        spl_autoload_register("ecm_autoload");
        // 加载常量
        Conf::load(ROOT_PATH . '/framework/data/constant.php');

        /* 加载初始化文件 */
        include_once ROOT_PATH . '/framework/eccore/controller/base.app.php'; //基础控制器类
        include_once ROOT_PATH . '/framework/eccore/controller/base.mod.php'; //模型基础类
        include_once ROOT_PATH . '/framework/eccore/comm.function.php';

        // 加载扩展文件
        if (!empty($config['external_libs'])) {
            foreach ($config['external_libs'] as $lib) {
                // 加入判断文件是否存在的容错性
                if (file_exists($lib)) {
                    include_once $lib;
                }
            }
        }

        /* 数据过滤 */
        if (!MAGIC_QUOTES) {
            $_GET = addslashes_deep($_GET);
            $_POST = addslashes_deep($_POST);
            $_REQUEST = addslashes_deep($_REQUEST);
            $_COOKIE = addslashes_deep($_COOKIE);
        }

        /* 请求转发 */
        $default_app = $config['default_app'] ? $config['default_app'] : 'index';
        $default_act = $config['default_act'] ? $config['default_act'] : 'index';

        // 当前模块及操作

        if(!empty($_REQUEST['action']) && !$_REQUEST['act'])    //适配第三方访问规则
            $_REQUEST['act'] = $_REQUEST['action'];

        $app = !empty($_REQUEST['app']) ? trim($_REQUEST['app']) : $default_app;
        $act = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : $default_act;
        //$script_name = $_SERVER['SCRIPT_NAME'] ? strtolower(substr($_SERVER['SCRIPT_NAME'], 1)) : strtolower(substr($_SERVER['DOCUMENT_URI'], 1));

	    $app = lcfirst($app);
	    $act = lcfirst($act);
	
	    $app_file = $config['app_root'] . "/{$app}.app.php";
        if (!is_file($app_file)) {
            trigger_error("APP file {$app_file} not found", E_USER_ERROR);
        }
        // 加载控制器文件
        include_once $app_file;
        define('APP', $app);
        define('ACT', $act);
        $app_class_name = ucfirst($app) . 'App';
        session_start();
        /* 实例化控制器 */
        $app = new $app_class_name();
        $app->do_action($act); //转发至对应的Action
        $app->destruct();
    } // end of startup
}

/**
 * 配置管理器
 * @usage    none
 */
class Conf
{
    /**
     * 加载配置项
     * @param     mixed $conf
     * @return    bool
     */
    public static function load($conf)
    {
        $old_conf = isset($GLOBALS['ECMALL_CONFIG']) ? $GLOBALS['ECMALL_CONFIG'] : array();
        if (is_string($conf)) {
            $conf = include $conf;
        }
        if (is_array($old_conf)) {
            $GLOBALS['ECMALL_CONFIG'] = array_merge($old_conf, $conf);
        } else {
            $GLOBALS['ECMALL_CONFIG'] = $conf;
        }
    }
    /**
     * 获取配置项
     * @param     string $k
     * @return    mixed
     */
    public static function get($key = '')
    {
        $vkey = $key ? strtokey("{$key}", '$GLOBALS[\'ECMALL_CONFIG\']') : '$GLOBALS[\'ECMALL_CONFIG\']';

        return eval('if(isset(' . $vkey . '))return ' . $vkey . ';else{ return null; }');
    }
}

/**
 * 实例化模型
 * @author 刘小祥
 * @date 2015-6-27
 * @param string $model_name 模型名称|表名称
 * @return object (BaseMod)
 */
function &m($model_name = '')
{
    $model_name = trim($model_name);
    if (!$model_name) {
        $model_name = 'base';
    }

    $tmpModName = $model_name;
    static $models = array();
    if (!isset($models[$tmpModName])) {
        $model_file = APP_PATH . "/mod/{$model_name}.mod.php";
        if (!file_exists($model_file)) {
            $models[$tmpModName] = new CBaseMod($model_name);
        } else {
            include_once $model_file;
            $model_name = ucfirst($model_name) . 'Mod';
            $models[$tmpModName] = new $model_name();
        }
    }

    return $models[$tmpModName];
}
	
	/**
	 * 加载配置文件
	 * @author 刘小祥 (2016年5月10日)
	 * @param string $name 配置文件名称(不加php扩展名)
	 */
	function inc($name) {
		if (isset($GLOBALS[$name])) {
			$msgConfig = $GLOBALS[$name];
		} else {
			$file = ROOT_PATH."/framework/data/{$name}.php";
			$msgConfig = include($file);
		}
		return $msgConfig;
	}

/**
 * 导入一个类
 * @param string $item - 文件名
 * @return    void
 */
function import($item)
{
    return include_once ROOT_PATH . "/framework/includes/libraries/{$item}.php";
}

/**
 * 导入一个类，并返回该类的对象 importClass
 * -- 原方法为class
 *
 * @param string $item
 * @return object
 */

function &ic($item, $obj = '')
{
    static $ic = array();
    if (!isset($ic[$item])) {
        $file = APP_PATH . "/classes/{$item}.class.php";
        if (!file_exists($file)) {
            trigger_error("ic not found {$file} ", E_USER_ERROR);
            exit();
        }
        include_once $file;
        $name = ucfirst($item);
        if ($obj) {
            $ic[$name] = new $name($obj);
        } else {
            $ic[$name] = new $name();
        }
    }
    return $ic[$name];
}
	
	if (defined('IS_API')) {
		function message($msg = "系统繁忙，请稍候再试" , $success = false , $data = array()){
			$msg =  array("success" => $success , "msg" => $msg , "data" => $data);
			
			if($msg['success'])
				$msg['code'] = 10000;
			else
				$msg['code'] = 90000;


            file_put_contents("/www/test3.txt", json_encode($msg)."\n", FILE_APPEND);

			return $msg;
		}
	}else{
		function message($msg = "操作成功" , $success = true , $data = array()){
			$msg =  array("success" => $success , "msg" => $msg , "data" => $data);
			
			return $msg;
		}
	}

/**
 * 将default.abc类的字符串转为$default['abc']
 * @param     string $str
 * @return    string
 */
function strtokey($str, $owner = '')
{
    if (!$str) {
        return '';
    }
    if ($owner) {
        return $owner . '[\'' . str_replace('.', '\'][\'', $str) . '\']';
    } else {
        $parts = explode('.', $str);
        $owner = '$' . $parts[0];
        unset($parts[0]);
        return strtokey(implode('.', $parts), $owner);
    }
}

/**
 * 创建MySQL数据库对象实例
 * @return  object
 */
function db($dbconfig = DB_CONFIG)
{
    static $DB = array();
    $dkey = substr(md5($dbconfig), 8, 16);
    if (!isset($DB[$dkey])) {
        import("db/ShowsunDB");
        $db = new ShowsunDB($dbconfig);
        $db->execute("SET NAMES utf8mb4");
        if (isset($_REQUEST['APIDATA'])) {
            $db->setDebugMode(1);
            $db->setDebugHandle(function ($sql) {
                log::jsonInfo(func_get_args());
            });
        } else {
            $db->setDebugHandle("sqlDebug");
        }
        $DB[$dkey] = $db;
    }
    return $DB[$dkey];
}

/**
 * 获得当前的域名
 * @return  string
 */
function get_domain()
{
    /* 协议 */
    $protocol = (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';
    /* 域名或IP地址 */
    if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
        $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
    } elseif (isset($_SERVER['HTTP_HOST'])) {
        $host = $_SERVER['HTTP_HOST'];
    } else {
        /* 端口 */
        if (isset($_SERVER['SERVER_PORT'])) {
            $port = ':' . $_SERVER['SERVER_PORT'];
            if ((':80' == $port && 'http://' == $protocol) || (':443' == $port && 'https://' == $protocol)) {
                $port = '';
            }
        } else {
            $port = '';
        }
        if (isset($_SERVER['SERVER_NAME'])) {
            $host = $_SERVER['SERVER_NAME'] . $port;
        } elseif (isset($_SERVER['SERVER_ADDR'])) {
            $host = $_SERVER['SERVER_ADDR'] . $port;
        }
    }

    return $protocol . $host;
}

/**
 * 获得网站的URL地址
 *
 * @return  string
 */
function siteUrl()
{
    return get_domain() . substr(PHP_SELF, 0, strrpos(PHP_SELF, '/'));
}

/**
 * 获得当前页面的全部URL地址
 *
 * @return  string
 */
function pageUrl()
{
    return get_domain() . $_SERVER['REQUEST_URI'];
}

/**
 * 将后台参数引入到GET数组中
 */
function do_get()
{
    global $argv, $argc;
    if ($argv) {
        for ($i = 1; $i < $argc; $i++) {
            $arr = explode('=', $argv[$i]);
            $_REQUEST[trim($arr[0])] = trim($arr[1]);
        }
    }
}

/**
 * 递归方式的对变量中的特殊字符进行转义
 * @access  public
 * @param   mix     $value
 * @return  mix
 */
function addslashes_deep(&$value, $trip_tags = false)
{
    if (empty($value)) {
        return $value;
    } else {
        if (true == $trip_tags) {
            if (!is_array($value)) {
                $value = addslashes(trim(strip_tags($value)));
                return $value;
            } else {
                array_walk($value, 'addslashes_deep', $trip_tags);
                return $value;
            }
        } else {
            return is_array($value) ? array_map('addslashes_deep', $value) : addslashes(trim($value));
        }
    }
}

function stripcslashes_deep(&$value, $trip_tags = false)
{
    if (empty($value)) {
        return $value;
    } else {
        if (true == $trip_tags) {
            if (!is_array($value)) {
                $value = stripcslashes(trim(strip_tags($value)));
                return $value;
            } else {
                array_walk($value, 'stripcslashes_deep', $trip_tags);
                return $value;
            }
        } else {
            return is_array($value) ? array_map('stripcslashes_deep', $value) : stripcslashes(trim($value));
        }
    }
}

/**
 * 将对象成员变量或者数组的特殊字符进行转义
 *
 * @access   public
 * @param    mix        $obj      对象或者数组
 * @author 刘阳(alexdany@126.com)
 *
 * @return   mix                  对象或者数组
 */
function addslashes_deep_obj($obj)
{
    if (is_object($obj) == true) {
        foreach ($obj as $key => $val) {
            if (($val) == true) {
                $obj->$key = addslashes_deep_obj($val);
            } else {
                $obj->$key = addslashes_deep($val);
            }
        }
    } else {
        $obj = addslashes_deep($obj);
    }
    return $obj;
}

/**
 * 递归方式的对变量中的特殊字符去除转义
 *
 * @access  public
 * @param   mix     $value
 *
 * @return  mix
 */
function stripslashes_deep($value)
{
    if (empty($value)) {
        return $value;
    } else {
        return is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
    }
}

/**
 * 编码转换函数
 *
 * @author 刘阳(alexdany@126.com)
 * @param string $source_lang       待转换编码
 * @param string $target_lang         转换后编码
 * @param string $source_string      需要转换编码的字串
 * @return string
 */
function ecm_iconv($source_lang, $target_lang, $source_string = '')
{
    static $chs = null;

    /* 如果字符串为空或者字符串不需要转换，直接返回 */
    if ($source_lang == $target_lang || $source_string == '' || preg_match("/[\x80-\xFF]+/", $source_string) == 0) {
        return $source_string;
    }

    if ($chs === null) {
        import('iconv.lib');
        $chs = new Chinese(ROOT_PATH . '/');
    }
    return $chs->Convert($source_lang, $target_lang, $source_string);
    return strtolower($target_lang) == 'utf-8' ? addslashes(stripslashes($chs->Convert($source_lang, $target_lang, $source_string))) : $chs->Convert($source_lang, $target_lang, $source_string);
}

/**
 * 移动文件（如果当前环境是开发或者测试环境，进行拷贝,否则进行移动）
 * @param string $input
 * @param string $newFile
 */
function ecm_rename($input, $path = "")
{
    $input = str_replace(IMG_URL, '', $input);
    $value = $input;
    if (!$value) {
        return '';
    }

    $array = explode("/", $value);
    if (in_array("temp", $array)) {
        $oldFile = ATTACHEMENT_PATH . "/{$value}";
        $tmpFile = str_replace("temp", $path, $value);
        $newFile = ATTACHEMENT_PATH . "/{$tmpFile}";

        $tempArray = explode("/", $newFile);
        $fileName = $tempArray[count($tempArray) - 1];
        unset($tempArray[count($tempArray) - 1]);
        $newPath = implode("/", $tempArray);
        ecm_mkdir($newPath, ATTACHEMENT_PATH);

        @copy($oldFile, $newFile);
        @unlink($oldFile);
        if ("SERVER" == MILIEU || "TEST" == MILIEU) {
            //@rename($oldFile , $newFile) ;
            //unlink($oldFile) ;
        } else if ("RD" == MILIEU) {
            //@copy($oldFile, $newFile ) ;
            //@rename($oldFile , $newFile) ;
            //unlink($oldFile) ;
        }
        return $tmpFile;
    } else {
        return $value;
    }
}
/**
 * 创建目录（如果该目录的上级目录不存在，会先创建上级目录）
 * 依赖于 $root_path 常量，且只能创建 $root_path 目录下的目录
 * 目录分隔符必须是 / 不能是 \
 * @param   string  $absolute_path  绝对路径
 * @param   int     $mode           目录权限
 * @return  bool
 */
function ecm_mkdir($absolute_path, $root_path = DIR_PATH, $mode = 0777)
{
    if (is_dir($absolute_path)) {
        return true;
    }

    $root_path = $root_path;
    $relative_path = str_replace($root_path, '', $absolute_path);
    $each_path = explode('/', $relative_path);
    $cur_path = $root_path; // 当前循环处理的路径
    foreach ($each_path as $path) {
        if ($path) {
            $cur_path = $cur_path . '/' . $path;
            if (!is_dir($cur_path)) {
                if (@mkdir($cur_path, $mode)) {
                    fclose(fopen($cur_path . '/index.htm', 'w'));
                } else {
                    return false;
                }
            }
        }
    }

    return true;
}

/**
 * 删除目录,不支持目录中带 ..
 * @param string $dir
 * @return boolen
 */
function ecm_rmdir($dir)
{
    $dir = str_replace(array('..', "\n", "\r"), array('', '', ''), $dir);
    $ret_val = false;
    if (is_dir($dir)) {
        $d = @dir($dir);
        if ($d) {
            while (false !== ($entry = $d->read())) {
                if ($entry != '.' && $entry != '..') {
                    $entry = $dir . '/' . $entry;
                    if (is_dir($entry)) {
                        ecm_rmdir($entry);
                    } else {
                        @unlink($entry);
                    }
                }
            }
            $d->close();
            $ret_val = rmdir($dir);
        }
    } else {
        $ret_val = unlink($dir);
    }

    return $ret_val;
}

/**
 * 设置COOKIE
 *
 * @access public
 * @param  string $key     要设置的COOKIE键名
 * @param  string $value   键名对应的值
 * @param  int    $expire  过期时间
 * @return void
 */
function ecm_setcookie($key, $value, $expire = 0, $cookie_path = COOKIE_PATH, $cookie_domain = COOKIE_DOMAIN)
{
    $cookie_domain = substr(SITE_URL, 11);
    setcookie($key, $value, $expire, $cookie_path, $cookie_domain);
}

/**
 * 获取COOKIE的值
 *
 * @access public
 * @param  string $key    为空时将返回所有COOKIE
 * @return mixed
 */
function ecm_getcookie($key = '')
{
    return isset($_COOKIE[$key]) ? $_COOKIE[$key] : 0;
}

/**
 * 对数组转码
 *
 * @param   string  $func
 * @param   array   $params
 *
 * @return  mixed
 */
function ecm_iconv_deep($source_lang, $target_lang, $value)
{
    if (empty($value)) {
        return $value;
    } else {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = ecm_iconv_deep($source_lang, $target_lang, $v);
            }
            return $value;
        } elseif (is_string($value)) {
            return ecm_iconv($source_lang, $target_lang, $value);
        } else {
            return $value;
        }
    }
}

/**
 * fopen封装函数
 *
 * @author 刘阳(alexdany@126.com)
 * @param string $url
 * @param int    $limit
 * @param string $post
 * @param string $cookie
 * @param boolen $bysocket
 * @param string $ip
 * @param int    $timeout
 * @param boolen $block
 * @return responseText
 */
function ecm_fopen($url, $limit = 500000, $post = '', $cookie = '', $bysocket = false, $ip = '', $timeout = 15, $block = true)
{
    //$return = '';
    $matches = parse_url($url);
    $host = $matches['host'];
    $path = $matches['path'] ? $matches['path'] . ($matches['query'] ? '?' . $matches['query'] : '') : '/';
    $port = !empty($matches['port']) ? $matches['port'] : 80;

    if ($post) {
        $out = "POST $path HTTP/1.0\r\n";
        $out .= "Accept: */*\r\n";
        //$out .= "Referer: $boardurl\r\n";
        $out .= "Accept-Language: zh-cn\r\n";
        $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
        $out .= "Host: $host\r\n";
        $out .= 'Content-Length: ' . strlen($post) . "\r\n";
        $out .= "Connection: Close\r\n";
        $out .= "Cache-Control: no-cache\r\n";
        $out .= "Cookie: $cookie\r\n\r\n";
        $out .= $post;
    } else {
        $out = "GET $path HTTP/1.0\r\n";
        $out .= "Accept: */*\r\n";
        //$out .= "Referer: $boardurl\r\n";
        $out .= "Accept-Language: zh-cn\r\n";
        $out .= "User-Agent: " . $_SERVER["HTTP_USER_AGENT"] . "\r\n";
        $out .= "Host: $host\r\n";
        $out .= "Connection: Close\r\n";
        $out .= "Cookie: $cookie\r\n\r\n";
    }
    $fp = @fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
    if (!$fp) {
        return '';
    } else {
        stream_set_blocking($fp, $block);
        stream_set_timeout($fp, $timeout);
        @fwrite($fp, $out);
        $status = stream_get_meta_data($fp);
        $return = "";
        if (!$status['timed_out']) {
            while (!feof($fp)) {
                if (($header = @fgets($fp)) && ($header == "\r\n" || $header == "\n")) {
                    break;
                }
            }

            $stop = false;
            while (!feof($fp) && !$stop) {
                $data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
                $return .= $data;
                if ($limit) {
                    $limit -= strlen($data);
                    $stop = $limit <= 0;
                }
            }
        }
        @fclose($fp);
        return $return;
    }
}

/**
 * 如果系统不存在file_put_contents函数则声明该函数
 *
 * @author 刘阳(alexdany@126.com)
 * @param   string  $file
 * @param   mix     $data
 * @return  int
 */
if (!function_exists('file_put_contents')) {
    define('FILE_APPEND', 'FILE_APPEND');
    if (!defined('LOCK_EX')) {
        define('LOCK_EX', 'LOCK_EX');
    }

    function file_put_contents($file, $data, $flags = '')
    {
        $contents = (is_array($data)) ? implode('', $data) : $data;
        $mode = ($flags == 'FILE_APPEND') ? 'ab+' : 'wb';
        if (($fp = @fopen($file, $mode)) === false) {
            return false;
        } else {
            $bytes = fwrite($fp, $contents);
            fclose($fp);
            return $bytes;
        }
    }
}

/**
 * 从文件或数组中定义常量
 * @author 刘阳(alexdany@126.com)
 * @param     mixed $source
 * @return    void
 */
function ecm_define($source)
{
    if (is_string($source)) {
        /* 导入数组 */
        $source = include $source;
    }
    if (!is_array($source)) {
        /* 不是数组，无法定义 */
        return false;
    }
    foreach ($source as $key => $value) {
        if (is_string($value) || is_numeric($value) || is_bool($value) || is_null($value)) {
            /* 如果是可被定义的，则定义 */
            define(strtoupper($key), $value);
        }
    }
}

/**
 * 获取当前时间的微秒数
 *
 * @author 刘阳(alexdany@126.com)
 * @return    float
 */
function ecm_microtime()
{
    if (PHP_VERSION >= 5.0) {
        return microtime(true);
    } else {
        list($usec, $sec) = explode(" ", microtime());

        return ((float) $usec + (float) $sec);
    }
}

/**
 * 定义常用基类自动加载函数
 * @author 刘小祥
 * @date 2015-8-15
 * @return bool
 */
function ecm_autoload($classname)
{
    if (strpos($classname, "\\")) {
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $classname);
        $file = LIB_PATH . DIRECTORY_SEPARATOR . $path . ".php";
        require_once $file;
        return true;
    }
    $tail = substr($classname, -3);
    switch ($tail) {
        case "Mod":
            $ext = "mod";
            break;
        case "App":
            $ext = "app";
            break;
        default:
            $ext = "class";
            break;
    }

    if ($ext != "class") {
        $filename = substr($classname, 0, -3) . ".{$ext}.php";
    } else {
        $filename = $classname . ".{$ext}.php";
    }
    $filename[0] = strtolower($filename[0]);
    $filename[1] = strtolower($filename[1]);
    $classFile = ROOT_PATH . "/framework/includes/common/{$filename}";
    if (is_file($classFile)) {
        include_once $classFile;
        return true;
    }
    return false;
}

/**
 * smarty挂件
 *
 * @param unknown_type $app
 * @param unknown_type $act
 * @param unknown_type $param
 */
function component($app, $act, $param = false)
{
    $app_file = APP_ROOT . "/{$app}.app.php";
    if (!is_file($app_file)) {
        trigger_error("APP not found", E_USER_ERROR);
        exit();
    }
    define('component', true);
    $_GET["component"] = $param;
    //echo $app_file;
    // 加载控制器文件
    include_once $app_file;
    define('APP', $app);
    define('ACT', $act);
    $app_class_name = ucfirst($app) . 'App';
    /* 实例化控制器 */
    $app = new $app_class_name();
    $app->$act($param); //转发至对应的Action

    $app->destruct();
}

/**
 * 获取/设置session值(session封装,利于以后扩展)
 * @author 刘小祥
 * @date 2015-8-14
 * @param string $name
 * @param mixed $value
 * @return mixed 不指定value时返回session值，否则修改session值
 */
function session($name, $value = false)
{
    if ($value) {
        $_SESSION[$name] = $value;
    } else {
        if (!isset($_SESSION[$name])) {
            return false;
        }

        if ($value === null) {
            unset($_SESSION[$name]);
            return;
        }
        return $_SESSION[$name];
    }
}
