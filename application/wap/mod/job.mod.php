<?php
	
	/**
	 * 职位模型
	 * Created by Malcolm.
	 * Date: 2018/3/6  15:36
	 */
	class JobMod extends CBaseMod {
		public function __construct (  ) {
			parent::__construct('job');
		}
		
		
		
		public function getInfo ( $id , $isDetail = false ) {
			$info = parent::getInfo($id);
			
			//薪水单位
                        switch ($info['money_type']) {
                            case 1:
                                $info['money_type_name'] = '元/月';
                                $info['money_name'] = $info['type'] == 2?'月结':'月薪';
                                break;

                            case 2:
                                $info['money_type_name'] = ' 元/天';
                                $info['money_name'] = $info['type'] == 2?'日结':'日薪';
                                break;

                            case 3:
                                $info['money_type_name'] = '元/年';
                                $info['money_name'] = '年薪';
                                break;

                            case 4:
                                $info['money_type_name'] = '元/周';
                                $info['money_name'] = $info['type'] == 2?'周结':'周薪';
                                break;

                            case 5:
                                $info['money_type_name'] = '元/天';
                                $info['money_name'] = '次日结';
                                break;

                            default:
                                $info['money_type_name'] = '元/月';
                                $info['money_name'] = $info['type'] == 2?'月结':'月薪';
                        }
			
			
			//标签
			$tagArr = explode(',',$info['tag_ids']);
			$info['apply_num'] = m('jobApply')->getApplyCount($id);
			if ( is_array($tagArr) ) {
				foreach ( $tagArr as $key => $val ) {
					$info['tag_name'][] = [
						'tag_id' =>$val,
						'tag_name' =>Zeus::config('job_tag',$val),
					];
			    }
			}
			
			//性别
			switch ($info['sex']){
				case 0:
					$info['sex_name'] = '不限';
					break;
					
				case 1:
					$info['sex_name'] = '男';
					break;
					
				case 2:
					$info['sex_name'] = '女';
					break;
					
				default:
					$info['sex_name'] = '不限';
			}
			
			//地址
			$info['city_name'] = $this->cityMod->getCityNameByDepth($info['dist_id'],2);
			//需求人数
                        $info['people_num'] = $info['type'] == 1?$info['num_lower'] .' / '.$info['num_upper']:$info['num_lower'];
			//日期
			$info['add_date'] = date('Y/m/d',$info['add_time']);
			
			//公司
			$companyInfo = $this->companyMod->getInfo($info['company_id']);
			
			$info['company_name'] = $companyInfo['name'];
			
			$info['company_logo'] = $companyInfo['logo'];
                        
                        //联系方式
                        $info['link_type'] = $info['link_type']?:1;
                        //岗位名称
                        $job_industry = m('job_industry')->getInfo($info['industry_id']);
                        //岗位名称
                        $info['industry_name'] = $job_industry['name'];
                        //距今
                        $add_time = $info['add_time'];
                        $dtime = time () - $add_time;  
                        switch ($dtime){
                            case $dtime < 60:
                                $info['time_shaft'] = '刚刚';  
                                break;
                            case $dtime < 60 * 60:
                                $min = floor ( $dtime / 60 ); 
                                $info['time_shaft'] = $min . '分钟前';  
                                break;
                            case $dtime < 60 * 60 * 24:
                                $h = floor ( $dtime / (60 * 60) ); 
                                $info['time_shaft'] = $h . '小时前 '; 
                                break;
                            case $dtime < 60 * 60 * 24 * 3:
                                $d = floor ( $dtime / (60 * 60 * 24) );  
                                if ($d == 1) 
                                    $info['time_shaft'] = '昨天 ';  
                                else  
                                    $info['time_shaft'] = '前天 ';  
                                break;
                            default :
                                $info['time_shaft'] = date('Y.m.d',$add_time); 
                                break;
                        }
                        //联系方式
                        $info['link_type_name'] = $info['link_type']== 1?'手机':($info['link_type'] == 2?'微信':'QQ');
                        //上班时间（多个）
                        $info['time_group'] = explode(',',$info['time_group']);
                        //判断是否连续
                        $info['time_group_is'] = $this->timeCheck($info['time_group']);
			if($isDetail){
				$info['company_status'] = $companyInfo['status'];
				$info['company_address'] = $this->cityMod->getCityName($companyInfo['dist_id']).$companyInfo['address'];
				$info['company_tel'] = $companyInfo['tel'];
				
				$info['add_date'] = date('Y/m/d H:i',$info['add_time']);
				
				switch ($companyInfo['status']){
					case 1:
						$info['company_status_name'] = '已认证';
						break;
						
					case 2:
						$info['company_status_name'] = '未认证';
						break;
						
					default:
						$info['company_status_name'] = '未认证';
				}
				
			}
			
			return $info;
		}
		
		/**
		 * @todo    获取我的收藏列表
		 * @author Malcolm  (2018年03月09日)
		 */
		public function getListInfo($id){
			$info = $this->getInfo($id);
			
			$data = [
				'job_id' => $info['id'],
				'job_type' => $info['type'],
				'job_title' => $info['title'],
				'job_money' =>$info['money_lower'].$info['money_type_name'],
				'job_company_name' => $info['company_name'],
				'job_company_id' => $info['company_id'],
				'job_tag_list' => $info['tag_name'],
				'job_company_logo' => $info['company_logo'],
				'job_city_name' => $info['city_name'],
				'job_add_date' => date('Y/m/d',$info['add_time']),
                                'job_industry_id'  => $info['industry_id'],
                                'job_industry_name'=> $info['industry_name'],
                                'job_time_shaft'   => $info['time_shaft'],
			];
			
			if(1==$info['type'])
				$data['job_money'] = Hera::humanPrice($info['money_lower'],false).'-'.Hera::humanPrice($info['money_upper']);
			
			return $data;
		}
		
		
		/**
		 * @todo    获取新任务列表信息
		 * @author Malcolm  (2018年04月12日)
		 */
		public function getTaskListInfo($id){
			$info = $this->getInfo($id);
			
			$data = [
				'task_id' => $info['id'],
				'task_title' => $info['title'],
				
				'task_city_name' => $info['city_name'],
				'task_add_date' => date('Y/m/d',$info['add_time']),
			];
			
			return $data;
		}
		
		
		/**
		 * @todo    获取职位详细信息
		 * @author Malcolm  (2018年04月13日)
		 */
		public function getDetailInfo($id){
			$info = $this->getInfo($id,true);
			
			if(!$info['title'] || $info['mark'] != 1)
				return [];
			
			$data = [
				'job_id' =>$info['id'],
				'job_type' =>$info['type'],
				'job_title' =>$info['title'],
				'job_city_name' =>$info['city_name'],
				'job_add_date' =>$info['add_date'],
				
				'job_money' =>$info['money_lower'].$info['money_type_name'],
                                'job_industry_name'   => $info['industry_name'],
				'job_company_id' =>$info['company_id'],
				'job_company_status' =>$info['company_status'],
				'job_company_status_name' =>$info['company_status_name'],
				'job_company_name' =>$info['company_name'],
				'job_company_log' =>$info['company_log'],
				'job_company_address' =>$info['company_address'],
				'job_company_tel' =>$info['company_tel'],
				'job_look_num' => $info['look_num'],
				'job_tag_list' => $info['tag_name'],
				'job_content' => $info['content'],
				'job_address' => m('city')->getCityName($info['dist_id']).$info['address'],
				
				//人数
				'job_people_num' => $info['people_num'],
				'job_sex_name' => $info['sex_name'] ,
				'job_language' => $info['language'] ,
				'job_experience' => $info['experience'] ,
				'job_work_date' => $info['date_start'] .' - '.$info['date_end'],
				'job_work_time' => $info['time_start'] .' - '.$info['time_end'],
				
				'job_link_type'    => $info['link_type'], //7.12迭代(兼职)
                                'job_link_type_name' => $info['link_type_name'], //7.17迭代(兼职)
				'job_link_person' => $info['link_person'],
				'job_link_phone' => $info['link_phone'],
				'job_share_url' => WAP_URL.'/?app=share&act=job&id='.$info['id'],
                            
                                //7.12迭代(兼职)
                                'job_age_lower_ex'  => $info['age_lower'] ,
                                'job_age_upper_ex'  => $info['age_upper'] , 
                                'job_is_continue'   => $info['is_continue'] == 1?'是':'否',//7.12迭代(兼职)
                                'job_is_interview'  => $info['is_interview'] == 1?'是':'否', //7.12迭代(兼职)
                                'job_time_group'    => $info['time_group'],
                                'job_lng'           => $info['lng'], //7.17迭代(兼职)
                                'job_lat'           => $info['lat'], //7.17迭代(兼职)
                                'job_apply_num'     => $info['apply_num'],  //7.17迭代(兼职)
                                'job_time_shaft'    => $info['time_shaft'], //7.17迭代(兼职)
			];
			
			if(1==$info['type'])
				$data['job_money'] = Hera::humanPrice($info['money_lower'],false).'-'.Hera::humanPrice($info['money_upper']);
			
			$typeList = explode(',',$info['job_type_ids']);
			
			$name = [];
			
			if ( is_array($typeList) ) {
				foreach ( $typeList as $key => $val ) {
					if($val)
						$name[] = Zeus::config('job_type_list',$val);
			    }
			}
			
			$data['job_sub_type_name'] = implode(' ',$name);
			
                        //工作经验
                        $jobTime = explode(',' , $info['experience']);
                        $name = [];

                        if (is_array($jobTime)) {
                            foreach ($jobTime as $key => $val) {
                                if ($val)
                                    $name[] = Zeus::config('job_time_list' , $val);
                            }
                        }

                        $data['job_experience'] = implode(' ' , $name);
			return $data;
		}
		
		public function  timeCheck($array){
                    for($i=1; $i<count($array); $i++){
                        $lastone = strtotime($array[$i-1]);
                        $thisone = strtotime($array[$i]);
                        if($thisone - $lastone != 3600*24)
                                return false;
                    }
                    return true;
                }
		
		
		
		
	}