<?php
	/**
	 * Created by Malcolm
	 * Date: 2017/9/16 11:51
	 */
	
	/**
	 * Created by Malcolm.
	 * Date: 2017/9/16  11:51
	 */
	class UserMod extends CBaseMod {
		public function __construct(){
			parent::__construct('user');
			
			$this->source = array(
				1	=>	'APP注册',
				2	=>	'后台添加'
			);
			$this->isEnabled = array(
				1	=>	'启用',
				2	=>	'停用'
			);
		}
		
		
		/**
		 * @todo    根据手机号获取推送别名
		 * @author Malcolm  (2017年09月22日)
		 */
		public function getDeviceIdByMobile($mobile){
			$query['cond'] = "mobile={$mobile} AND mark=1";
			$query['order_by'] = "id DESC";
			$info =  $this->getOne($query);
			
			return $info['device_id'];
		}
		
		
		/**
		 * @todo    根据手机号获取用户ID
		 * @author Malcolm  (2017年09月22日)
		 */
		public function getUserIdByMobile($mobile){
			$query['cond'] = "mobile={$mobile} AND mark=1";
			$query['order_by'] = "id DESC";
			$info =  $this->getOne($query);
			
			return $info['id'];
		}
		
		
		/**
		 * @todo    获取最简介信息
		 * @author Malcolm  (2017年11月23日)
		 */
		public function getMiniInfo($id){
			$info = $this->getInfo($id);
			
			$data = [
				'id' =>$info['id'],
				'name' =>$info['nickname'],
				'mobile' =>$info['mobile'],
			];
			
			return $data;
		}
		
		
		/**
		 * @todo    获取示例用户
		 * @author Malcolm  (2017年12月06日)
		 */
		public function getSampleUserIds(){
			$mobileArr = [
				'13100000001',
				'13100000002',
				'13100000003',
				'13100000004',
				'13100000005',
				'13100000006',
				'13100000007',
				'13100000008',
				'13100000009',
				'13100000010',
			];
			
			
			$mobile = implode(',',$mobileArr);
			
			$cond = " mobile in({$mobile}) AND mark = 1 ";
			
			$ids = $this->getIds($cond);
			
			return $ids;
		}
		
		
		
		
		
		
		
		
	}