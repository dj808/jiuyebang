<?php
	
	/**
	 * 职位申请模型
	 * Created by Malcolm.
	 * Date: 2018/3/6  15:36
	 */
	class JobApplyMod extends CBaseMod {
		public function __construct (  ) {
			parent::__construct('job_apply');
		}
		
		
		
		public function getInfo ( $id ) {
			$info = parent::getInfo($id);
			
			$info['jobInfo'] = unserialize($info['snap']);
			
			switch ($info['status']){
				case 1:
					$info['status_name'] = '投递成功';
					break;
					
				case 2:
					$info['status_name'] = '已查阅';
					break;
					
				case 3:
					$info['status_name'] = '待审核';
					break;
					
				case 4:
					$info['status_name'] = '审核通过';
					break;
					
				case 5:
					$info['status_name'] = '审核未通过';
					break;
					
				default:
					$info['status_name'] = '投递成功';
			}
			
			
			return $info;
		}
		
		
		/**
		 * @todo    获取我的兼职记录列表信息
		 * @author Malcolm  (2018年04月11日)
		 */
		public function getMyHalfJobList($id){
			$info = $this->getInfo($id);
			
			//成长值计算
			$growth = ceil($info['jobInfo']['money_lower']/10);
			
			$data = [
				'job_id' =>$info['job_id'],
				'job_title' =>$info['jobInfo']['title'],
				'job_apply_time' =>date('Y/m/d H:i:s',$info['add_time']),
				'job_growth' =>$growth,
			];
			
			return $data;
		}
		
		
		/**
		 * @todo    获取我的投递列表信息
		 * @author Malcolm  (2018年04月11日)
		 */
		public function getMyListInfo($id){
			$info = $this->getInfo($id);
			
			$data = [
				'job_id' => $info['jobInfo']['id'],
				'job_apply_id' => $info['id'],
				'job_title' => $info['jobInfo']['title'],
				'job_money' =>$info['jobInfo']['money_lower'].$info['jobInfo']['money_type_name'],
				'job_company_name' => $info['jobInfo']['company_name'],
				'job_company_id' => $info['jobInfo']['company_id'],
				'job_tag_list' => $info['jobInfo']['tag_list'],
				'job_company_logo' => $info['jobInfo']['company_logo'],
				'job_city_name' => $info['jobInfo']['city_name'],
				'job_apply_status' => $info['status'],
				'job_apply_status_name' => $info['status_name'],
				'job_apply_date' =>date('Y/m/d',$info['add_time'])
			];
			
			if(1==$info['jobInfo']['type'])
				$data['job_money'] = Hera::humanPrice($info['jobInfo']['money_lower'],false).'-'.Hera::humanPrice($info['jobInfo']['money_upper']);
			
			
			return $data;
		}
		
		
		/**
		 * @todo    获取我的投递列表信息
		 * @author Malcolm  (2018年04月12日)
		 */
		public function getMyJobTaskListInfo($id){
			$info = $this->getMyListInfo($id);
			
			$data = [
				'task_apply_id' => $info['job_apply_id'],
				'task_title' => $info['job_title'],
				
				'task_city_name' => $info['job_city_name'],
				'task_add_date' => $info['job_apply_date'],
			];
			
			return $data;
		}
		
                
        /**
         * @todo    获取申请人数
         * @author Malcolm  (2018年06月20日)
         */
		public function getApplyCount($jobId){
		    $cond = " is_task = 2 AND job_id = {$jobId} AND mark = 1 ";

		    $count = $this->getCount($cond);

		    return $count;
        }
		
	}