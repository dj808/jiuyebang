<?php
	/**
	 * 城市管理控制器
	 */
if(!defined('IN_ECM')) {die ('Forbidden');}
class CityApp extends BackendApp{

	public $mod,$ic;
	
	public function __construct(){
		parent::__construct(); 

		$this->mod = m('city');
		false && $this->mod = new CityMod();
		
		
	}
	
	
	/**
	 * @todo    开放城市管理
	 * @author wangqs  (2017年04月12日)
	 */
	public function index(){
		//所有省
		$provinceList = $this->mod->getChildList(1);
		
		//默认省份
		$cityList = $this->mod->getChildList(1387);
		
		$this->assign("provinceList", $provinceList);
		$this->assign("cityList", $cityList);
		
		parent::index();
	}
	
	
	/**
	 * @todo    修改城市开启状态
	 * @author wangqs  (2017年04月12日)
	 */
	public function setStatus(){
		if (IS_POST) {
			$id = intval($_POST['id']);
			$isOpen = intval($_POST['is_open']);
			
			$re = $this->mod->edit(['is_open'=>$isOpen],$id);
			
			if(!$re)
				$this->ajaxReturn(R("开启失败", false));
			
			$this->ajaxReturn(R('操作成功' , true));
		}
	}
	
	
	
	/**
	 * 城市联动挂件
	 * @author gumin <gumin@aiyishu.com> (2017年3月9日)
	 * @param int $provinceId 省
	 * @param int $cityId   市
	 * @param int $districtId 区
	 * @return  array
	 */
	public function select($provinceId = 0, $cityId = 0, $districtId = 0) {
	    if (IS_POST) {
	        $pid = intval($_POST['pid']);
	        if($pid == 0){
		        $list = [];
	        }else{
		        $list = $this->mod->getChildList($pid);
	        }
	        
	        $this->ajaxReturn(R("获取成功", true, $list));
	    }
	    
	    //所有省
	    $provinceList = $this->mod->getChildList(1);
	    
	    //获取省下面的市
	    if ($provinceId > 0) {
	        $cityList = $this->mod->getChildList($provinceId);
	    }
	    
	    //不显示区
	    if($districtId == -1) {
	    	$this->assign('noDistrict', 1);
	    } else {
	    	//获取市下面的区
	    	if ($cityId > 0) {
	    		$districtList = $this->mod->getChildList($cityId);
	    	}
	    }
	    
	    $this->assign("districtList", $districtList);
	    $this->assign("cityList", $cityList);
	    $this->assign("provinceList", $provinceList);
	    $this->assign("provinceId", $provinceId);
	    $this->assign("cityId", $cityId);
	    $this->assign("districtId", $districtId);
	    $this->display("component/selectCity.html");
	}
	
	
	
}
