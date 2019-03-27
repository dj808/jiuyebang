<?php
	
	/**
	 * 培训订单模型
	 * Created by Malcolm.
	 * Date: 2018/2/5  11:08
	 */
	class TrainingMod extends CBaseMod {
		public function __construct() {
			parent::__construct('training');
		}
		
		
		/**
		 * @todo    获取列表信息
		 * @author Malcolm  (2018年04月16日)
		 */
		public function getListInfo($id){
			$info = parent::getInfo($id);
			
			$data = [
				'train_id' =>$info['id'],
				'train_cover' =>$info['cover'],
				'train_title' =>$info['title'],
				'train_city_name' =>$this->cityMod->getCityNameByDepth($info['area_id'],2),
				'train_price' =>$info['price'].'元',
				'train_add_date' =>date('Y/m/d',$info['add_time']),
			];
			
			if($data['train_price']==0)
				$data['train_price'] = '免费';
			
			return $data;
		}
		
		
		/**
		 * @todo    获取详情
		 * @author Malcolm  (2018年04月16日)
		 */
		public function getDetailInfo($id){
			$info = parent::getInfo($id);
			
			$data = [
				'train_id' =>$info['id'],
				'train_title' =>$info['title'],
				'train_sub_title' =>$info['sub_title'],
				'train_price' =>$info['price'],
				'train_address' =>$this->cityMod->getCityName($info['area_id']).$info['address'],
				'train_tel' =>$info['tel'],
				'train_class_num' =>$info['class_num'],
				'train_class_duration' =>$info['class_duration'],
				'train_class_date' =>$info['class_date'],
				'train_class_intro' =>$info['class_intro'],
				'train_is_coupon' =>$info['is_coupon'],
				'train_share_url' =>WAP_URL."/?app=share&act=train&id={$info['id']}",
			];
			
			if($data['train_price']==0)
				$data['train_price'] = '免费';
			
			return $data;
		}
		
	}