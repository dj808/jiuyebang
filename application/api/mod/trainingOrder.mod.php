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


        /**
         * @todo    获取培训订单详情
         * @author wangqs  (2017年04月18日)
         */
        public function getOrderInfo($id){
            $info = parent::getInfo($id);

            $cityMod = m('city');
            false && $cityMod = new CityMod();


            $data = [
                'order_id' => $info['id'],
                'title' => $info['title'],
                'sub_title' => $info['sub_title'],
                'province_id' => $info['province_id'],
                'city_id' => $info['city_id'],
                'area_id' => $info['area_id'],
                'area' => $info['area_id'] ? $cityMod->getCityName($info['area_id']) : "",
                'address' => $info['address']?$info['address']:'',
                'tel' => $info['tel']?$info['tel']:'',
                'class_num' => $info['class_num'],
                'class_duration' => $info['class_duration'],
                'class_date' => $info['class_date'],
                'user_id' => $info['user_id'],
                'realname' => $info['realname'],
                'contact_tel' => $info['contact_tel'],
                'email' => $info['email'],
                'message' => $info['message'],
                'price' => $info['price'],
                'total_fee' => $info['total_fee'],
                'user_coupon_id' => $info['user_coupon_id']?$info['user_coupon_id']:'',
                'pay_type' => $info['pay_type'],
                'pay_status' => $info['pay_status'],
                'coupon_price' => $info['coupon_price']?$info['coupon_price']:'',
                'order_no' => $info['order_no'],

                'add_time' => date('Y-m-d H:i',$info['add_time']),
            ];


            if($info['pay_time']>0) {
                $data['pay_time'] = date('Y-m-d H:i',$info['pay_time']);
            }else{
                $data['pay_time'] ='';
            }

            if($data['pay_status'] ==1 )
                $data['pay_status_name'] = '未支付';
            if($data['pay_status'] ==2 )
                $data['pay_status_name'] = '已支付';
            if($data['pay_status'] ==3 )
                $data['pay_status_name'] = '支付失败';
            if($data['pay_status'] ==4 )
                $data['pay_status_name'] = '已取消';

            return $data;
        }


        /**
         * @todo    获取申请人数
         * @author Malcolm  (2018年06月20日)
         */
        public function getApplyCount($jobId){
            $cond = "training_id = {$jobId} AND mark = 1 ";

            $count = $this->getCount($cond);

            return $count;
        }

	}