<?php
/**
 * 控制器基础类
 * @date 2014-03-01
 * 未经zongqz允许，该文件任何人不得更改
 */
if (!defined('IN_ECM')) {die('Forbidden');}
class BaseApp
{
    protected $data; //系统执行需要的变量
    protected $difference; //main后台，fornt前台
    protected $smarty;
    public $params;

    public function __construct()
    {
    	
        $this->assign('app', APP);
        $this->assign('act', ACT);
        // $this->assign('imgUrl', IMG_URL);
        if (MILIEU == 'RD' || $_GET['debug_time']) {
            $startTime = ecm_microtime();
            $this->assign("startTime", $startTime);
        }
        
        $this->params = I('');
    }
    public function destruct()
    {
        //$GLOBALS['db']->close();
        //unset($GLOBALS['db']);
    }

    /**
     * 运行指定的动作
     */
    public function do_action($action)
    {
        if ($action && $action{0} != '_' && method_exists($this, $action)) {
            $this->_curr_action = $action;
            $this->_run_action(); //运行动作
        } else {
            exit(' 请指定正确的方法名' . APP . '::' . ACT);
        }
    }

    // 默认操作
	public function index()
	{
		$this->display();
	}

    /**
     * 给视图传递变量
     * @param     string $k
     * @param     mixed  $v
     * @return    void
     */
    public function assign($k, $v = null)
    {
        $this->data[$k] = $v;
    }

    /**
     * 显示视图
     * @param     string $n
     * @return    void
     */
    protected function display($tpl = '')
    {
        include_once ROOT_PATH . '/framework/includes/smarty/Smarty.class.php';
        $smarty = $this->initSmarty();
        $tmp = $this->data;
        foreach ($tmp as $key => $val) {
            $smarty->assign($key, $val);
        }
        $tpl = !empty($tpl) ? $tpl : APP . '/' . ACT . '.html';
        $smarty->display($tpl);
    }
	
	
    /**
     * 输出模板的内容
     *
     * @param string $tpl 模板
     * @return void
     * @author matengfei
     * @date 2014-05-10
     */
    protected function show($tpl = 'index.php')
    {
        $tpl = TPL_ROOT . '/' . $tpl;
        extract($this->data);
        try {
            require $tpl;
        } catch (Exception $e) {
            echo $tpl;
            die();
        }
    }

    /**
     *  获取模板输出内容
     *
     * @author  <shengwx>
     * @return string
     */
    protected function fetch($tpl = 'index.html')
    {
        include_once ROOT_PATH . '/framework/includes/smarty/Smarty.class.php';
        $smarty = $this->initSmarty();
        $tmp = $this->data;
        foreach ($tmp as $key => $val) {
            $smarty->assign($key, $val);
        }
        $content = $smarty->fetch($tpl);
        return $content;

    }

    /**
     * 获得模板输出的内容
     *
     * @return string
     * @author matengfei
     * @date 2014-05-10
     */
    protected function getOutput($tpl = 'index.php')
    {
        $tpl = TPL_ROOT . '/' . $tpl;
        ob_start();
        extract($this->data);
        require $tpl;
        return ob_get_clean();
    }

    /**
     * 获取程序运行时间
     * @param:     int $precision
     * @return:    float
     */
    protected function _get_run_time($precision = 5)
    {
        return round(ecm_microtime() - START_TIME, $precision);
    }

    /**
     * 运行动作
     * @param    none
     * @return    void
     */
    protected function _run_action()
    {
        $action = $this->_curr_action;
        $this->$action();
    }

    protected function initSmarty()
    {
        if ($this->smarty) {
            return $this->smarty;
        }
        $smarty = new Smarty();
        $smarty->debugging = false;
        $smarty->template_dir = TPL_ROOT;
        $smarty->compile_dir = APP_PATH . "/temp/smarty/template_c/{$this->difference}/";
        $smarty->config_dir = APP_PATH . "/temp/smarty/configs/{$this->difference}/";
        $smarty->cache_dir = APP_PATH . "/temp/smarty/cache/{$this->difference}/";
        $smarty->caching = false;
        $this->smarty = $smarty;
        return $this->smarty;
    }

    /**
     * Action跳转(URL重定向） 支持指定模块和延时跳转
     * @access protected
     * @param string $url 跳转的URL表达式
     * @param array $params 其它URL参数
     * @param integer $delay 延时跳转的时间 单位为秒
     * @param string $msg 跳转提示信息
     * @return void
     */
    protected function redirect($url, $params = array(), $delay = 0, $msg = '')
    {
        list($app, $act) = explode('/', $url);
        $app = !empty($app) ? trim($app) : APP;
        $act = !empty($act) ? trim($act) : ACT;
        // 解析参数
        if (is_string($params)) {
            // aaa=1&bbb=2 转换成数组
            parse_str($params, $vars);
        } elseif (!is_array($params)) {
            $vars = array();
        }
        $domain = get_domain();
        $url = $domain . '?app=' . $app . '&act=' . $act;
        if (!empty($vars)) {
            $vars = http_build_query($vars);
            $url .= '&' . $vars;
        }
        redirect($url, $delay, $msg);
    }

    /**
     * Ajax方式返回数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type AJAX返回数据格式
     * @param int $json_option 传递给json_encode的option参数
     * @return void
     */
    protected function ajaxReturn($data, $type = '', $json_option = 0)
    {
        if (empty($type)) {
            $type = 'JSON';
        }
        switch (strtoupper($type)) {
            case 'JSON':
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode($data, $json_option));
            case 'XML':
                // 返回xml格式数据
                header('Content-Type:text/xml; charset=utf-8');
                exit(xml_encode($data));
            case 'JSONP':
            // // 返回JSON数据格式到客户端 包含状态信息
            // header('Content-Type:application/json; charset=utf-8');
            // $handler  =   isset($_GET[C('VAR_JSONP_HANDLER')]) ? $_GET[C('VAR_JSONP_HANDLER')] : C('DEFAULT_JSONP_HANDLER');
            // exit($handler.'('.json_encode($data,$json_option).');');
            case 'EVAL':
                // 返回可执行的js脚本
                header('Content-Type:text/html; charset=utf-8');
                exit($data);
            default:
	            header('Content-Type:application/json; charset=utf-8');
	            exit(json_encode($data, $json_option));
        }
    }



	/**
	 * @todo    写日志文件，主要用于脚本
	 * @author Malcolm  (2018年06月19日)
	 */
	public function editLogFile($log,$path='/www/logs/default.log'){
		$time = date("Y-m-d H:i:s");

		$log .= "  时间：{$time}  \n";

		echo $log;

		file_put_contents($path, $log, FILE_APPEND);
	}


}
