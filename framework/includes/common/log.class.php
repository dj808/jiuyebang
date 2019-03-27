<?php
class log
{
    /**模式:用户记录输入*/
    const MODE_INPUT = 1;
    /**模式:用户记录输出*/
    const MODE_OUTPUT = 2;
    /**模式:用户记录数据库信息*/
    const MODE_DB = 4;
    /**
     * 写入日志
     * @author 刘小祥 (2016年6月29日)
     * @param mixed 日志内容
     */
    public static function info($str, $fields = '')
    {
        if (!is_string($str)) {
            ob_start();
            print_r($str);
            $str = ob_get_contents();
            ob_end_clean();
        }
        $GLOBALS['log_content'] .= $str . "\n";
    }

    public static function jsonInfo($data, $field = '')
    {
        if (is_string($data)) {
            $arr = debug_backtrace();
            foreach ($arr as $row) {
                if (substr($row['file'], -9) == "class.php") {
                    $filename = pathinfo($row['file'], PATHINFO_BASENAME);
                    $preStr = "{$filename}:{$row['line']} {$row['class']}->{$row['function']} ";
                    $data = $preStr . $data;
                    break;
                }

            }
        }
        if ($field) {
            $GLOBALS['log_json'][$field] = $data;
        } else {
            $GLOBALS['log_json']['extra'][] = $data;
        }

    }

    public static function json($data, $field = '')
    {
        if (is_string($data)) {
            $arr = debug_backtrace();
            foreach ($arr as $row) {
                if (substr($row['file'], -9) == "class.php") {
                    $filename = pathinfo($row['file'], PATHINFO_BASENAME);
                    $preStr = "{$filename}:{$row['line']} {$row['class']}->{$row['function']} ";
                    $data = $preStr . $data;
                    break;
                }

            }
        }
        if ($field) {
            $GLOBALS['log_json'][$field] = $data;
        } else {
            $GLOBALS['log_json']['extra'][] = $data;
        }

    }

    /**
     * 获取日志内容
     * @author 刘小祥 (2016年6月29日)
     */
    public static function getContent()
    {
        if (defined('FILE_LOG') && FILE_LOG > 0) {
            return $GLOBALS['log_content'];
        }
    }

    /**
     * 写入日志
     * @author 刘小祥 (2016年6月29日)
     */
    public static function write($dir = "temp")
    {
        if ($GLOBALS['log_mode'] == 0) {
            return;
        }

        if (defined('FILE_LOG') && FILE_LOG > 0) {
            $logDir = LOG_PATH . "/" . $dir . date("/Y/m/d/h/") . APP . "/";
            if (!is_dir($logDir)) {
                mkdir($logDir, 0777, true);
            }
            $logFile = $logDir . ACT . ".log";
            $logStr = date("[Y-m-d H:i:s] ") . str_pad("#", 80, "#") . "\n";
            $logStr .= log::getContent();
            file_put_contents($logFile, $logStr, FILE_APPEND);
        }
    }

    public static function jsonWrite($keyName = 'default_log')
    {
        if ($GLOBALS['log_mode'] == 0) {
            return;
        }

        if ($GLOBALS['log_json']) {
            $data = $GLOBALS['log_json'];

            /*if(ACT=='sendSignCode'){
                file_put_contents("/www/jyb/test.txt", json_encode($data));
            }*/

            $data['time'] = time();
            $data['date'] = date('Y-m-d H:i:s', $data['time']);
            $content = json_encode($data);
            //压缩目的是: 减少内部流量
            $gzcontent = gzcompress($content);
            $redis = cache_server();
            $redis->server->lPush($keyName, $gzcontent);
        }
    }

    public static function setMode($mode)
    {
        $GLOBALS['log_mode'] = $mode;
    }

    /**
     * 判断是否包含日志模式
     * @author 刘小祥 (2016年6月29日)
     * @param int $mode 日志模式
     * @return boolean
     */
    public static function hasMode($mode)
    {
        //默认记录输入和输出日志
        if (!isset($GLOBALS['log_mode'])) {
            $GLOBALS['log_mode'] = log::MODE_INPUT | log::MODE_OUTPUT;
        }
        $u = $GLOBALS['log_mode'] | $mode;
        if ($u != $GLOBALS['log_mode']) {
            return false;
        }
        return true;
    }
}
