<?php
	/**
	 * Created by wangqs
	 * Date: 2017/4/18 16:31
	 */
	
	/**
	 * 用户优惠券
	 * Created by wangqs.
	 * Date: 2017/4/18  16:31
	 */
	class UserCouponMod extends CBaseMod {
		public function __construct () {
			parent::__construct('user_coupon');
		}
		
		
		/**
		 * @todo    获取列表数据
		 * @author Malcolm  (2018年02月05日)
		 */
		public function getListInfo ( $id ) {
			$info = $this->getInfo($id);
			
			$couponInfo = $this->couponMod->getInfo($info['coupon_id']);
			
			
			$data = [
				'coupon_id' =>$info['id'],
				'coupon_name' =>$couponInfo['name'],
				'use_time' =>$info['use_time']?date('Y-m-d',$info['use_time']):'',
				'start_time' =>$info['start_time']?date('Y-m-d',$info['start_time']):'',
				'end_time' =>$info['end_time']?date('Y-m-d',$info['end_time']):'',
				'coupon_price' =>$info['coupon_price'],
			];
			
			return $data;
		}
		
		
	}