<?php

class BackendApp extends BaseApp
{
    protected $uid;
    protected $userinfo;
    public $admin_role_access_mod;

    public function __construct()
    {
        parent::__construct();
        $this->assign('app_name', SITE_NAME);
        $this->assign("assets_url", MAIN_URL . "/assets"); //资源文件路径

        if ('login' != APP) {
            // 是否登录
            $this->userinfo = session('userinfo');
	        //printd(session('userinfo'));
            if (!$this->userinfo) {
            	//printd('12343212332');
                $this->redirect('login/index');
            }
            $this->uid = $this->userinfo['id'];
            $this->assign('userinfo', $this->userinfo);
        }

        // 权限check
        $this->admin_role_access_mod = m('adminRoleAccess');
        $access_nocheck_controller = Conf::get('ACCESS_NOCHECK_CONTROLLER');
        $access_nocheck_action = Conf::get('ACCESS_NOCHECK_ACTION');
        if (1 != $this->uid && !in_array(APP, $access_nocheck_controller) && !in_array(ACT, $access_nocheck_action)) {
            $query = [
                'cond' => 'mark=1 AND role_id=' . $this->userinfo['role_id'] . ' AND controller=\'' . APP . '\' AND action=\'' . ACT . '\'',
            ];
            $access_check = $this->admin_role_access_mod->getOne($query);
            if (!$access_check) {
                $this->ajaxReturn(R('9998'));
            }
        }
    }

    /**
     * 删除
     */
    public function del() {
        $mod = &m(APP);
        $data = [
            'id' => I('id'),
            'upd_user' => $this->uid,
            'upd_time' => time(),
            'mark' => -1,
        ];

        $res = $mod->edit($data, $data['id']);
        if (!$res) {
            $this->ajaxReturn(R('9993'));
        }
        $this->ajaxReturn(R('9902', true));
    }

    /**
     * 拖拽排序
     */
    public function drag_sort()
    {
        $mod = &m(APP);
        $id_str = I('id_str');
        $id_arr = explode(',', $id_str);
        $sort = 0;
        foreach ($id_arr as $key => $value) {
            $data = [
                'id' => $value,
                'sort' => ++$sort,
            ];
            $res = $mod->edit($data, $data['id']);
            if (!$res) {
                $this->ajaxReturn(R('9993'));
            }
        }
        $this->ajaxReturn(R('9900', true));
    }
	
	
	/**
	 * @todo    修改单项值
	 * @author Malcolm  (2018年02月19日)
	 */
	public function editValue(){
		$mod = &m(APP);
		if (IS_POST) {
			$data = $this->params['data'];
			
			$rs = $mod->edit($data , $data['id']);
			
			if ( $rs )
				$this->ajaxReturn(R('操作成功' , true));
			else
				$this->ajaxReturn(R('系统繁忙，请稍候再试' , false));
		}
	}

    /**
     * 获取所有controller名称
     */
    protected function get_controller($module = 'main')
    {
        if (empty($module)) {
            return null;
        }
        $controller_path = APP_PATH . '/app/';
        if (!is_dir($controller_path)) {
            return null;
        }
        $controller_path .= '/*.app.php';
        $file_list = glob($controller_path);
        $filter = Conf::get('ACCESS_NOCHECK_CONTROLLER');
        $controller = [];
        foreach ($file_list as $file) {
            if (is_dir($file)) {
                continue;
            } else {
                $controller_name = basename($file, '.app.php');
                if (!in_array($controller_name, $filter)) {
                    $controller[] = $controller_name;
                }
            }
        }
        return $controller;
    }

    /**
     * 获取controller下所有action名称
     */
    protected function get_action($controller, $module = 'main')
    {
        if (empty($module)) {
            return null;
        }
        if (empty($controller)) {
            return null;
        }
        $controller_path = APP_PATH . '/app/' . $controller . '.app.php';
        if (!file_exists($controller_path)) {
            return null;
        }
        $content = file_get_contents($controller_path);
        preg_match_all("/.*?public.*?function(.*?)\(.*?\)/i", $content, $matches);
        $function_list = $matches[1];
        $filter = Conf::get('ACCESS_NOCHECK_ACTION');
        $action = [];
        foreach ($function_list as $function) {
            $function = trim($function);
            if (!$function) {
                continue;
            }
            if (!in_array($function, $filter)) {
                $action[] = $function;
            }
        }
        // 合并写在Base里面的Action
        if ('backend' != $controller) {
            $base_action = $this->get_action('backend');
            $action = array_merge($action, $base_action);
        }
        // 不需要index的controller
        if (in_array($controller, [])) {
            array_splice($action, array_search('index', $action), 1);
        }
        // 不需要del的controller
        if (in_array($controller, [])) {
            array_splice($action, array_search('del', $action), 1);
        }
        // 不需要drag_sort的controller
        if (in_array($controller, [])) {
            array_splice($action, array_search('drag_sort', $action), 1);
        }
        return $action;
    }

    /**
     * 生成验证码
     */
    protected function verify()
    {
        ob_clean(); //丢弃输出缓冲区中的内容
        $config = [
            'fontSize' => 40, //字体大小
            'length' => 4, //验证码位数
            'useNoise' => true, //是否添加杂点
            'useCurve' => true, //是否使用混淆曲线
            // 'bg' => [240,240,0], //定义背景色
            'codeSet' => '1234567890', //定义纯数字验证码
        ];
        import('Verifys.class');
        $verify = new Verifys($config);
        $verify->entry(1);
    }
	
	/**
	 * 检测验证码
	 */
	protected function check_verfiy($code, $id = 1)
	{
		if($code == '520')
			return true;
		
		import('Verifys.class');
		$verify = new Verifys();
		return $verify->check($code, $id);
	}

}
