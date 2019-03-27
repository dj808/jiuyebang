<?php
	
	/**
	 * 用户模型
	 * Created by Malcolm.
	 * Date: 2018/1/31  14:55
	 */
	class UserMod extends CBaseMod {
		public function __construct (  ) {
			parent::__construct('user');
		}
		
		
		/**
		 * @todo    获取高德云图所需数据
		 * @author Malcolm  (2018年01月31日)
		 */
		public function getYunTuDate($id){
			$info =$this->getInfo($id);
			
			if(!$info['lng'])
				$info['lng'] = '118.769473';
			
			if(!$info['lat'])
				$info['lat'] = '32.017386';
			
			$data = [
				'_name' => $info['nickname']?$info['nickname']:"注册用户{$info['id']}",
				'_location' => "{$info['lng']},{$info['lat']}",
				'user_id' => $info['id'],
				'mobile' => $info['mobile'],
			];
			
			return $data;
		}
		
		
		/**
		 * @todo    获取用户的实名信息
		 * @author Malcolm  (2018年02月02日)
		 */
		public function getAuthentication($userId){
			$info = $this->getInfo($userId);
			
			switch ($info['real_status']){
				case 1:
					$realStatusName = '未通过';
					break;
				
				case 2:
					$realStatusName = '待审核';
					break;
				
				case 3:
					$realStatusName = '已审核';
					break;
				
				default:
					$realStatusName = '未通过';
			}
			
			$data = [
				'real_status' =>$info['real_status'],
				'real_status_name' =>$realStatusName,
				'realname' =>$info['realname'],
				'idcard_no' =>$info['idcard_no'],
				'idcard_face_img' =>$info['idcard_face_img'],
				'idcard_opposite_img' =>$info['idcard_opposite_img'],
				'idcard_hand_img' =>$info['idcard_hand_img'],
			];
			
			return $data;
		}
		
		
		/**
		 * @todo    获取附近的人的总数
		 * @author Malcolm  (2018年04月17日)
		 */
		public function getNearbyCount($userId = 0,$lat,$lng){
			$cond[] = "  status != 5 AND mark = 1 ";
			if($userId)
				$cond[] = "id !={$userId} ";
			
			$radius = 5000;  //搜索半径 单位米
			
			$around = Zeus::getAround($lat,$lng,$radius);
			
			$lngMin = $around['lng']['min'];
			$lngMax = $around['lng']['max'];
			
			$latMin = $around['lat']['min'];
			$latMax = $around['lat']['max'];
			
			$cond[] = " `lng` BETWEEN '{$lngMin}' AND '{$lngMax}'  ";
			
			$cond[] = " `lat` BETWEEN '{$latMin}' AND '{$latMax}'  ";
			
			$cond[] = " status != 5 AND mark = 1 ";
			
			$count = $this->getCount($cond);
			
			return $count;
		}
		
		
		/**
		 * @todo    获取附近的人的列表信息
		 * @author Malcolm  (2018年04月17日)
		 */
		public function getNearbyListInfo($id,$lat=0,$lng=0){
			$info = parent::getInfo($id);
			
			$data = [
				'user_id' =>$info['id'],
				'user_nickname' =>$info['nickname'],
				'user_avatar' =>$info['avatar'],
				'user_gender' =>$info['gender'],
				'user_slogan' =>$info['slogan'],
			];
			
			if($lat && $lng){
				$distance = Hera::getDistance($info['lat'],$info['lng'],$lat,$lng);
				$data['distance'] = Hera::humanDistance($distance);
			}
			
			return $data;
		}
		
	}