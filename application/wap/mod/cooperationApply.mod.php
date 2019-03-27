<?php
	
	/**
	 * 互助申请模型
	 * Created by Malcolm.
	 * Date: 2018/3/6  15:36
	 */
	class CooperationApplyMod extends CBaseMod {
		public function __construct (  ) {
			parent::__construct('cooperation_apply');
		}
		
		
		/**
		 * @todo    获取我的列表
		 * @author Malcolm  (2018年03月09日)
		 */
		public function getMyListInfo($id){
			$info = $this->getInfo($id);
			
			$userInfo = $this->userMod->getInfo($info['user_id']);
			
			$data = [
				'cooperation_id'=>$info['cooper_id'],
				'apply_user_id'=>$info['user_id'],
				'apply_user_name'=>$userInfo['name'],
			];
			
			return $data;
		}
		
		
		/**
		 * @todo    获取我的互助成长记录
		 * @author Malcolm  (2018年04月11日)
		 */
		public function myHelpGrowthList($id){
			$info = $this->getInfo($id);
			
			$info['cooperationInfo'] = unserialize($info['snap']);
			
			$data = [
				'cooper_id' => $info['cooper_id'],
				'cooper_title' => $info['cooperationInfo']['title'],
				'cooper_apply_time' => date('Y/m/d H:i:s',$info['add_time']),
				'cooper_growth' => 5,
			];
			
			return $data;
		}
		
		
		
	}