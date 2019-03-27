<?php
	/**
	 * @todo    组件管理 - 控制器
	 * @author Malcolm  (2018年04月09日)
	 */
	class ComponentApp extends BackendApp {
		
		/**
		 * 构造函数
		 */
		public function __construct() {
			parent::__construct();
		}
		
		
		/**
		 * @todo    城市选择挂件
		 * @author Malcolm  (2018年04月09日)
		 */
		public function selectCity($param) {
			//{'component'|component:'selectCity':"1387(编辑时候的省选中项),1388(编辑时候的市选中项),1390(编辑时候的区选中项)"}
			$cityMod = m('city');
			false && $cityMod = new CityMod();
			
			$pArr = explode(',', $param);
			$selectedProvince = intval($pArr[0]);
			$selectedCity = intval($pArr[1]);
			$selectedArea = intval($pArr[2]);
			
			if ($selectedProvince && $selectedCity && $selectedArea) {
				//获取省
				$provinceList = $cityMod->getChildByPid();
				$this->assign('selectedProvince', $selectedProvince);
				$this->assign('provinceList', $provinceList);
				
				//获取市
				$cityList = $cityMod->getChildByPid($selectedProvince);
				$this->assign('selectedCity', $selectedCity);
				$this->assign('cityList', $cityList);
				
				//获取区
				$areaList = $cityMod->getChildByPid($selectedCity);
				$this->assign('selectedArea', $selectedArea);
				$this->assign('areaList', $areaList);
			} else {
				//获取省
				$provinceList = $cityMod->getChildByPid();
				$this->assign('provinceList', $provinceList);
			}
			
			$this->display('component/selectCity.html');
		}
		
		
		
	}