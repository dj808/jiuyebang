<?php
	
	/**
	 * 配置相关控制器
	 * Created by Malcolm.
	 * Date: 2018/2/19  21:42
	 */
	class ConfigApp extends BackendApp {
		public $mod, $ic;
		
		public function __construct() {
			parent::__construct();
			
			$this->mod = m('config');
			false && $this->mod = new ConfigMod();
			
			$this->ic = ic('config');
			false && $this->ic = new Config();
		}
		
		
		/**
		 * @todo    列表
		 * @author Malcolm  (2018年02月19日)
		 */
		public function ajaxList() {
			$cond[] = "mark=1";
			
			if($this->params['name'])
				$cond[] = " `name` LIKE '%{$this->params['name']}%' ";
			
			$data = Zeus::pageData([
				'cond'=>$cond,
				'order_by' =>' sort ASC'
			],$this->mod,'getListInfo');
			
			$this->ajaxReturn($data);
		}
		
		
		/**
		 * @todo    编辑修改
		 * @author Malcolm  (2018年02月19日)
		 */
		public function  edit() {
			$id = (int) $_REQUEST['id'];

			if (IS_POST) {
				$result = $this->ic->edit($_POST, $id);

				$this->ajaxReturn($result);
			}
			$configGroupMod =  m("configGroup");
			$configGroupList = $configGroupMod->getData([
				'cond'=>"mark=1",
				'order_by'=>"sort  ASC, id DESC"
			]);
			$info = [];
			if ($id) {
				$info = $this->mod->getInfo($id);
			}
			$this->assign("types", $this->mod->type);
			$this->assign("configGroupList", $configGroupList);
			$this->assign("info", $info);
			$this->display("config/edit.html");
		}
		
		
	}