<?php
	
	/**
	 * 个人简历模型
	 * Created by Malcolm.
	 * Date: 2018/2/1  11:39
	 */
	class ResumeMod extends CBaseMod {
		public function __construct() {
			parent::__construct("resume");
		}
		
		
		/**
		 * @todo    获取用户简历完成度
		 * @author Malcolm  (2018年02月01日)
		 */
		public function getFullByUserId($userId){
			$cond = " user_id = {$userId} AND mark = 1 ";
			
			$ids = $this->getIds($cond);
			
			if(!$ids[0])
				return 0;
			
			$info = $this->getInfo($ids[0]);
			//按区域划分
			
			$full['base'] = ['name','graduated','birthday','province_id','city_id','area_id','address','tel'];
			
			$full['edu'] = ['education_exp'];
			
			$full['job'] = ['job_exp'];
			
			$full['self'] = ['evaluation'];
			
			$full['photo']= ['student_photo','student_photo'];
			
			$intact = 0;
			
			if ( is_array($full) ) {
				foreach ( $full as $key => $val ) {
					$tmp =0;
					if ( is_array($val) ) {
						foreach ( $val as $k => $v ) {
							if($info[$v])
								$tmp++;
					    }
					}
					
					
					if($tmp>0)
						$intact +=20;
					
			    }
			}
			
			return $intact;
		}
		
		
		/**
		 * @todo    根据用户ID 获取简历信息
		 * @author Malcolm  (2018年02月01日)
		 */
		public function getInfoByUserId($userId){
			$userInfo = $this->userMod->getInfo($userId);
			
			$cond = " user_id = {$userId} AND mark = 1 ";
			
			$ids = $this->getIds($cond);
			
			$info = $this->getInfo($ids[0]);
			
			
			$info['user_avatar'] = $userInfo['avatar'];
			$info['user_nickname'] = $userInfo['nickname'];
			
			$info['resume_id'] = $info['id'];
			
			unset($info['id']);
			
			$info['user_id'] = $info['user_id']?$info['user_id']:0;
			$info['name'] = $info['name']?$info['name']:'';
			$info['gender'] = $info['gender']?$info['gender']:3;
			$info['graduated'] = $info['graduated']?$info['graduated']:0;
			$info['birthday'] = $info['birthday']?$info['birthday']:'';
			$info['province_id'] = $info['province_id']?$info['province_id']:0;
			$info['city_id'] = $info['city_id']?$info['city_id']:0;
			$info['area_id'] = $info['area_id']?$info['area_id']:0;
			$info['address'] = $info['address']?$info['address']:'';
			$info['tel'] = $info['tel']?$info['tel']:'';
			
			switch ($info['gender']){
				case 1:
					$info['gender_name'] = '男';
					break;
				
				case 2:
					$info['gender_name'] = '女';
					break;
				
				case 3:
					$info['gender_name'] = '保密';
					break;
				
				default:
					$info['gender_name'] = '保密';
			}
			
			switch ($info['graduated']){
				case 1:
					$info['graduated_name'] = '已毕业';
					break;
				
				case 2:
					$info['graduated_name'] = '未毕业';
					break;
				
				default:
					$info['graduated_name'] = '';
			}
			
			
			
			
			if($info['area_id']){
				$info['area_name'] = $this->cityMod->getCityName($info['area_id']);
			}else{
				$info['area_name'] = '';
			}
			
			
			if($info['education_exp'])
				$info['education_exp'] = unserialize($info['education_exp']);
			else
				$info['education_exp'] =[
					'school_id'=>'',
					'school_name'=>'',
					'major_id'=>'',
					'major_name'=>'',
					'education_id'=>'',
					'education_name'=>'',
				];
				
			
			if($info['job_exp'])
				$info['job_exp'] = unserialize($info['job_exp']);
			else
				$info['job_exp'][0] = [
					'type_id'=>'',
					'type_name'=>'',
					'name'=>'',
					'job_time_id'=>'',
					'job_time_name'=>'',
					'note'=>'',
				];
			
			$info['evaluation'] = $info['evaluation']?$info['evaluation']:'';
			$info['student_photo'] = $info['student_photo']?$info['student_photo']:'';
			$info['health_photo'] = $info['health_photo']?$info['health_photo']:'';
			
			unset($info['add_time']);
			unset($info['mark']);
			
			//完成度
			$info['intact'] = $this->getFullByUserId($userId);
			
			return $info;
		}
		
		
		/**
		 * @todo    编辑
		 * @author Malcolm  (2018年02月05日)
		 */
		public function editByUser($data,$userId) {
			//获取用户简历ID
			$cond = " user_id = {$userId} AND mark = 1 ";
			
			$ids = $this->getIds($cond);
			
			$id = $ids[0]?$ids[0]:0;
			
			$rs = $this->edit($data,$id);
			
			return $rs;
		}
		
		
	}