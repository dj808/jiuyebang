<?php
	
	class CompanyMod extends CBaseMod {
		
		/**
		 * 构造函数
		 */
		public function __construct () {
			parent::__construct('company');
		}
		
		
		/**
		 * @todo    获取详细信息
		 * @author Malcolm  (2018年04月14日)
		 */
		public function getDetailInfo($id){
			$info = parent::getInfo($id);
			
			switch ($info['status']){
				case 1:
					$info['status_name'] = '已认证';
					break;
					
				case 2:
					$info['status_name'] = '未认证';
					break;
					
				default:
					$info['status_name'] = '未认证';
					
			}
			
			$info['cityName'] = $this->cityMod->getCityNameByDepth($info['dist_id'],2);
			
			$data = [
				'company_id' => $info['id'],
				'company_name' => $info['name'],
				'company_logo' => $info['logo'],
				'company_slogan' => $info['slogan'],
				'company_status' => $info['status'],
				'company_status_name' => $info['status_name'],
				'company_address' => $this->cityMod->getCityName($info['dist_id']).$info['address'],
				'company_link_phone' => $info['link_phone'],
			];
			
			return $data;
		}
		
	}
