<?php
	
	/**
	 * 趣事模型
	 * Created by Malcolm.
	 * Date: 2018/3/6  15:24
	 */
	class FunMod extends CBaseMod {
		public function __construct() {
			parent::__construct('fun');
			
		}
		
		/**
		 * @todo    获取我的发布列表信息
		 * @author Malcolm  (2018年03月06日)
		 */
		public function getMyListInfo($id){
			$info = $this->getInfo($id);
			
			$data = [
				'fun_id' =>$info['id'],
				'title' =>$info['title'],
				'look_num' =>$info['look_num'],
				'add_date' =>date('Y-m-d',$info['add_time']),
			];
			
			return $data;
		}
		
	}