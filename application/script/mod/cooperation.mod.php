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
		
	}