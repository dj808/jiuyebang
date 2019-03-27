<?php
	
	/**
	 * 配置组管理控制器
	 * Created by Malcolm.
	 * Date: 2018/2/19  14:20
	 */
	class ConfigGroupApp  extends BackendApp
	{
		public $mod, $configIc;
		
		public function __construct() {
			parent::__construct();
			
			$this->mod = m('configGroup');
			false && $this->mod = new ConfigGroupMod();
			
			/*$this->ic = ic('configGroup');
			false && $this->ic = new ConfigGroup();*/
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
			],$this->mod,'getShortInfo');
			
			$this->ajaxReturn($data);
		}
		
		
		/**
		 * @todo    修改单项值
		 * @author Malcolm  (2018年02月19日)
		 */
		public function editValue(){
			if (IS_POST) {
				$data = $this->params['data'];
				
				$rs = $this->mod->edit($data , $data['id']);
				
				if ( $rs )
					$this->ajaxReturn(R('操作成功' , true));
				else
					$this->ajaxReturn(R('系统繁忙，请稍候再试' , false));
			}
		}
		
		
		/**
		 * @todo    编辑/修改
		 * @author Malcolm  (2018年02月19日)
		 */
		public function edit(){
			if (IS_POST) {
				$data = [
					'name' => $this->params['name'],
					'sort' => $this->params['sort'],
				];
				
				$res = $this->mod->edit($data, $this->params['id']);
				
				if (!$res) {
					$this->ajaxReturn(R('9993'));
				}
				$this->ajaxReturn(R('9901', true));
			}
			$info = [];
			if (!empty($this->params['id'])) {
				$info = $this->mod->getInfo($this->params['id']);
			}
			
			$this->assign('info', $info);
			$this->display();
		}
		
		
		
	}