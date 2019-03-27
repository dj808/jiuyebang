<?php
	
	/**
	 * 互助信息
	 * Created by Malcolm.
	 * Date: 2018/3/6  14:57
	 */
	class CooperationMod extends CBaseMod {
		public function __construct() {
			parent::__construct('cooperation');
			
		}
		
		
		/**
		 * @todo    获取我的发布列表信息
		 * @author Malcolm  (2018年03月06日)
		 */
		public function getMyListInfo($id){
			$info = $this->getInfo($id);
			
			switch ($info['type']){
				case 1:
					$typeName = '转让';
					break;
					
				case 2:
					$typeName = '互助';
					break;
				
				default:
					$typeName = '转让';
			}
			
			switch ($info['status']){
				case 1:
					$statusName = '未完成';
					break;
					
				case 2:
					$statusName = '已完成';
					break;
				
				default:
					$statusName = '未完成';
			}
			
			//留言次数
			$commentCond = " type = 5 AND parent_id = {$id} AND mark = 1 ";
			$commentCount = $this->commentMod->getCount($commentCond);
			
			//申请人数
			$applyCond = " coope_id = {$id} AND mark = 1 ";
			$applyCount = $this->cooperationApplyMod->getCount($applyCond);
			
			//地址
			$cityName = $this->cityMod->getCityNameByDepth($info['area_id'],2);
			
			$data = [
				'cooperation_id' =>$info['id'],
				'type' =>$info['type'],
				'type_name' =>$typeName,
				'status' =>$info['status'],
				'status_name' =>$statusName,
				'title' =>$info['title'],
				'look_num' =>$info['look_num'],
				'comment_count' =>$commentCount,
				'apply_count' =>$applyCount,
				'city_name' =>$cityName,
				'add_date' =>date('Y-m-d',$info['add_time']),
			];
			
			return $data;
		}
		
		
		/**
		 * @todo    获取列表信息
		 * @author Malcolm  (2018年04月14日)
		 */
		public function getListInfo ( $id,$lat='',$lng='' ) {
			$info = $this->getInfo($id);
			
			switch ($info['type']){
				case 1:
					$info['type_name'] = '转让';
					break;
				
				case 2:
					$info['type_name'] = '互助';
					break;
				
				default:
					$info['type_name'] = '转让';
			}
			
			switch ($info['status']){
				case 1:
					$info['status_name'] = '未完成';
					break;
				
				case 2:
					$info['status_name'] = '已完成';
					break;
				
				default:
					$info['status_name'] = '未完成';
			}
			
			$userInfo = $this->userMod->getInfo($info['user_id']);
			
			//计算距离
			
			if(!$lat || $lng){
				$lat = 32.03945;
				$lng = 118.78405;
			}
			
			$distance = Hera::getDistance($lat,$lng,$info['lat'],$info['lng']);
			
			$distance = Hera::humanDistance($distance);
			
			$data = [
				'cooperation_id' =>$info['id'],
				'cooperation_type' =>$info['type'],
				'cooperation_type_name' =>$info['type_name'],
				'cooperation_status' =>$info['status'],
				'cooperation_status_name' =>$info['status_name'],
				'cooperation_user_id' =>$userInfo['id'],
				'cooperation_user_avatar' =>$userInfo['avatar'],
				'cooperation_is_paid' =>$info['is_paid'],
				'cooperation_price' =>$info['price'].'元',
				'cooperation_title' =>$info['title'],
				'cooperation_content' =>$info['content'],
				'cooperation_city_name' =>$this->cityMod->getCityNameByDepth($info['area_id'],2,' '),
				'cooperation_distance' =>$distance,
				'cooperation_look_num' =>$info['look_num'],
				'cooperation_add_date' =>Hera::humanDate($info['add_time']),
			];
			
			return $data;
		}
		
		
		/**
		 * @todo    获取详情
		 * @author Malcolm  (2018年04月14日)
		 */
		public function getDetailInfo($id){
			$info = $this->getListInfo($id);
			
			$oldInfo = parent::getInfo($id);
			
			$userInfo = $this->userMod->getInfo($info['cooperation_user_id']);
			
			$info['cooperation_user_nickname'] = $userInfo['nickname'];
			
			switch ($info['cooperation_is_paid']){
				case 1:
					$info['cooperation_is_paid_name'] = '付费';
					break;
					
				case 2:
					$info['cooperation_is_paid_name'] = '免费';
					break;
					
				default:
					$info['cooperation_is_paid_name'] = '免费';
			}
			
			
			switch ($oldInfo['sex']){
				case 1:
					$info['cooperation_sex_req'] = '帅哥';
					break;
					
				case 2:
					$info['cooperation_sex_req'] = '美女';
					break;
				
				default:
					$info['cooperation_sex_req'] = '不限';
			}
			
			
			$info['cooperation_need_num'] = $oldInfo['need_num'];
			$info['cooperation_share_url'] = WAP_URL."/app=share&act=cooperation&id={$id}";
			
			return $info;
		}
		
	}