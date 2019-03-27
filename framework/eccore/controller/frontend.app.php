<?php
/**
 * 前台控制器基础类
 * @author HH
 * @date 2016-05-20
 */
class FrontendApp extends BaseApp {
	
	/**用户信息 */
	protected $userId,$userInfo,$TDK;
	
	/**
	 * 构造函数 *
	 * */
	
	
	function __construct() {
		parent::__construct();
		$this->tplPath = ROOT_PATH . "/template/" ;
		$this->assign("siteUrl" , SITE_URL );
		$this->assign("wwwUrl" , WWW_URL );
		$this->assign("manageUrl" , MAIN_URL );
		$this->assign("domain" , substr(WWW_URL,11,strlen(WWW_URL)-12));
		$this->assign('siteName' , SITE_NAME );
		$this->assign('staticUrl' , STATIC_URL);
		$this->assign('imgUrl' , IMG_URL);
		$this->_get_url_param();
		
		$this->assign('assets_url', WWW_URL . '/assets/');
		//导航产品分类
		//$this->assign('navCateList', $this->getNavCateList());
		
		//系统配置
		//$this->assign('configList', $this->getConfig());
		
		
		$this->getTdk();
		
		$this->userId = session('userId');
		
		/*$mallUserMod = m('user');
		false && $mallUserMod = new UserMod();
		$this->userInfo = $mallUserMod->getInfo($this->userId);
		$this->assign('userInfo', $this->userInfo);*/
		
		//判断浏览器环境
		$this->assign('wechat', $this->isWechat());
	}
	
	
	
	/**
	 * @todo    设置TDK
	 * @author wangqs  (2017年05月24日)
	 */
	protected function getTdk(){
		$tdkArr = inc('tdk.inc');
		
		//根据app 获取
		$tdkArr = $tdkArr[APP];
		$tdk = '';
		if ( is_array($tdkArr) ) {
			foreach ( $tdkArr as $key => $val ) {
				if(ACT == $key)
					$tdk = $val;
		    }
		}
		
		if(!$tdk)
			$tdk = [
				't' => '就业邦',
				'd' => '杜绝水机二次污染，颠覆传统净水工艺，新一代自冲洗，量子磁化富氢多功能净水器',
				'k' => '弘腾,弘腾净水器,净水器,净水器哪个牌子好,净水器什么牌子好,净水器排名,净水器厂家,磁化净水器',
			];
		
		$this->TDK =$tdk;
		$this->assign('TDK', $tdk);
	}
	
	//检查用户是否已经登录 
	protected function needLogin(){
		if(!session('userId')){
			if(IS_POST && ACT !='login')
				$this->jsonReturn(message('您已退出登录，请重新登录后再继续操作！',false));
			
			if(ACT !='login'){
				//获取当前地址
				$from = urlencode(pageUrl());
				
				header("Location:/?app=ucenter&act=login&from={$from}");
				exit();
			}
			
		}
			
        $this->userId = session('userId');
		$this->assign("userId" , session('userId'));
	}
	
	
	
	//前台URL处理
	private function _get_url_param(){
		$r_url = $_SERVER ['REQUEST_URI'];
		if ($r_url != '/' && false === strpos($r_url, "?")) {
			header("Location:/404/");
			return;
		}
		if (false != strpos($r_url, 'html?')) {
			$urls = explode("html?" , $r_url);
		}else {
			$urls = explode("?", $r_url);
		}
		if (false != strpos($urls[0], 'index.s')) {
			$curl = $urls[0].'html';
		}else{
			$curl = $urls[0].'index.shtml';
		}
		$site_url = substr(SITE_URL, 0,strlen(SITE_URL)-1);
		$curl = $site_url.$curl;
		$this->assign("curl" , $curl );
		$str = $urls[1];//url后面的参数
		$strs = explode("&", $str);
		if ($strs) {
			foreach ($strs as $val){
				$param = explode("=", $val);
				$_GET[$param[0]] = $param[1];
			}
		}
	}
	
	
	public function getConfig(){
		$configMod = m('config');
		false && $configMod = new ConfigMod();
		
		$data = $configMod->getConfigList();
		
		return $data;
	}
	
	/**
	 * 解析和获取模板内容
	 * @param  $tpl 模板文件名
	 * @author HH
	 * @date 2016-5-20
	 */
	protected function fetch($tpl = 'index.php') {
		// 页面缓存
		ob_start();
		ob_implicit_flush(0);
		$tpl = TPL_ROOT.'/'.$tpl;
		extract($this->data);
		try {
			require($tpl);
			$content = ob_get_clean();
			return $content ;
		} catch (Exception $e) {
			echo $tpl;
			die();
		}
	}
	
	
	/**
	 * 输出JSON数据
	 * @author 刘小祥
	 * @date 2016年3月8日
	 */
	public function jsonReturn() {
		$arr = func_get_args();
		if (!is_array($arr[0])) {
			$result = call_user_func_array("message", $arr);
		} else {
			$result = $arr[0];
		}
		echo json_encode($result);
		exit();
	}
	
	
	/**
	 * @todo    获取导航分类列表
	 * @author wangqs  (2017年05月30日)
	 */
	protected function getNavCateList(){
		$productCateMod = m('productCate');
		false && $productCateMod = new ProductCateMod();
		
		$data = $productCateMod->getChildByPid();
		if ( is_array($data) ) {
			foreach (  $data as &$dataCateList ) {
				$dataCateList['list'] = $productCateMod->getChildByPid($dataCateList['id']);
		    }
		}
		
		return $data;
	}
	
	
	
	
	public function render($tpl="", $data=array()) {
		if (empty($tpl)) $tpl = ACT.".html";
		if ($data) {
			foreach ($data as $name=>$value) {
				$this->assign($name, $value);
			}
		}
		
		$this->display("layouts/header.html");
		
		if(strpos($tpl,'/')){
			$this->display( $tpl );
		}else{
			$this->display( APP."/{$tpl}");
		}
		
		$this->display("layouts/foot.html");
	}


    public  function isWechat(){
		
		if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false )
			return true;
		
		return false;
	}
	
	
}
