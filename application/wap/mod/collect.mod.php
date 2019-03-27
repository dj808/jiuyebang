<?php
	
	/**
	 * 收藏模型
	 * Created by Malcolm.
	 * Date: 2018/3/6  15:36
	 */
	class CollectMod extends CBaseMod {
		public function __construct (  ) {
			parent::__construct('collect');
		}
		
		
		/**
		 * @todo    获取我的收藏列表
		 * @author Malcolm  (2018年03月09日)
		 */
		public function getListInfo($id){
			$info = $this->getInfo($id);
			
			switch ($info['type']){
				case 1:
					$mod = $this->jobMod;
					break;
					
				case 2:
					$mod = $this->jobMod;
					break;
				
				case 3:
					$mod = $this->funMod;
					break;
				
				case 4:
					$mod = $this->raidersMod;
					break;
				
				case 5:
					$mod = $this->raidersMod;
					break;
				
				case 6:
					$mod = $this->raidersMod;
					break;
				
				case 7:
					$mod = $this->newsMod;
					break;
				
				case 8:
					$mod = $this->trainingMod;
					break;
				
				default:
					$mod = $this->jobMod;
			}
			
			
			$data = $mod->getListInfo($info['type_id']);
			
			$data['type'] = $info['type'];
			$data['type_id'] = $info['type_id'];
			$data['collect_id'] = $info['id'];
			
			return $data;
		}
		
	}