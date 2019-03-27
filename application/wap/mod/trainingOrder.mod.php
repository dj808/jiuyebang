<?php
	
	/**
	 * 培训订单模型
	 * Created by Malcolm.
	 * Date: 2018/2/5  11:08
	 */
	class TrainingOrderMod extends CBaseMod {
		public function __construct() {
			parent::__construct('training_order');
		}
		
		
		/**
		 * @todo    获取培训成长记录列表信息
		 * @author Malcolm  (2018年02月05日)
		 */
		public function myTrainingGrowthList($id) {
			$info = $this->getInfo($id);
			
			$data = [
				'training_id'=>$info['training_id'],
				'training_order_id'=>$info['id'],
				'training_title'=>$info['title'],
				'training_buy_time'=>date('Y/m/d H:i:s',$info['add_time']),
				'training_growth'=>ceil($info['price']/10)
			];
			
			return $data;
		}
		
	}