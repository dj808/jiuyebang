<?php
	
	/**
	 * 职位控制器拓展
	 * Created by Malcolm.
	 * Date: 2018/4/13  14:28
	 */
	class Job extends Zeus {
		public $mod;
		public  function __construct() {
			parent::__construct("job");
			$this->mod = m('job');
			false&&$this->mod = new JobMod();
		}
		
		
		
		/**
		 * @todo    获取职位列表的搜索条件
		 * @author Malcolm  (2018年04月13日)
		 */
		public function getConditionForJob($param){
			$type = intval($param['type']);
			$cityId = intval($param['city_id']);
			
			//行业
			$industry = m('job_industry')->getData(['cond' => 'mark = 1','order_by' => 'sort DESC']);
			
			//结算方式
			if($type == 2){
				$money = [
					[
						'money_id' =>0,
						'money_name' =>'不限',
					],
					
					[
						'money_id' =>1,
						'money_name' =>'日结',
					],
					
					[
						'money_id' =>2,
						'money_name' =>'周结',
					],
					
					[
						'money_id' =>3,
						'money_name' =>'月结',
					],
                                        [
						'money_id' =>5,
						'money_name' =>'次日结',
					]
				];
			}else{
				$money = [
					[
						'money_id' =>0,
						'money_name' =>'不限',
					],
					
					[
						'money_id' =>1,
						'money_name' =>'2000 - 3000',
					],
					
					[
						'money_id' =>2,
						'money_name' =>'3000 - 5000',
					],
					
					[
						'money_id' =>3,
						'money_name' =>'5000 以上',
					],
				];
				
			}
			
			
			//地区
			$city = m('city')->getSubCity($cityId);
			
			//排序
			$order = [
				[
					'order_id' =>1,
					'order_name' =>'综合排序',
				],
				
				[
					'order_id' =>2,
					'order_name' =>'人气最高',
				],
				
				[
					'order_id' =>3,
					'order_name' =>'薪资最高',
				],
				
				[
					'order_id' =>4,
					'order_name' =>'薪资最低',
				],
				
			];
			
			
			$industryList = [];
                        $industryList[0]['industry_id'] = 0;
                        $industryList[0]['industry_name'] = '不限';
			if ( is_array($industry) ) {
				foreach ( $industry as $val ) {
					$industryList[] = [
						'industry_id' => $val['id'],
						'industry_name' => $val['name'],
					];
			    }
			}

			
			$city[0] = '不限';
			$distList = [];
			if ( is_array($city) ) {
				foreach ( $city as $key => $val ) {
					$distList[] = [
						'dist_id' => $key,
						'dist_name' => $val,
					];
			    }
			}
			$distList = Hera::arraySort($distList,'dist_id');
			
                        //7.12迭代
                        //发布时间
                        $pubtimeList = [
                            [    
                                'pubtime_id' =>0,
                                'pubtime_name' =>'不限',
                            ],
                            [
                                'pubtime_id' =>1,
                                'pubtime_name' =>'三天内',
                            ],
                            [
                                'pubtime_id' =>2,
                                'pubtime_name' =>'一周内',
                            ],
                            [
                                'pubtime_id' =>3,
                                'pubtime_name' =>'一周以上',
                            ]
                        ];
                        //性别要求
                        $sexList = [
                            [    
                                'sex_id' =>0,
                                'sex_name' =>'不限',
                            ],
                            [
                                'sex_id' =>1,
                                'sex_name' =>'男',
                            ],
                            [
                                'sex_id' =>2,
                                'sex_name' =>'女',
                            ]
                        ];
                        //距离范围
                        $distanceList = [
                            [    
                                'distance_id' =>0,
                                'distance_name' =>'不限',
                            ],
                            [
                                'distance_id' =>1,
                                'distance_name' =>'1公里内',
                            ],
                            [
                                'distance_id' =>2,
                                'distance_name' =>'5公里内',
                            ],
                            [
                                'distance_id' =>3,
                                'distance_name' =>'5公里以上',
                            ]
                        ];
                        //上班时间
                        $worktimeList = [
                            [    
                                'worktime_id' =>0,
                                'worktime_name' =>'不限',
                            ],
                            [
                                'worktime_id' =>1,
                                'worktime_name' =>'工作日',
                            ],
                            [
                                'worktime_id' =>2,
                                'worktime_name' =>'周末',
                            ]
                        ];
			$data = [
				'industry_list' =>$industryList,
				'money_list' =>$money,
				'dist_list' =>$distList,
				'order_list' =>$order,
                                'pubtime_list' => $pubtimeList,
                                'sex_list' => $sexList,
                                'distance_list' =>$distanceList,
                                'worktime_list' =>$worktimeList,
			];
			
			return message('操作成功',true,$data);
			
			
		}
		
		
		
		/**
		 * @todo    获取职位列表
		 * @author Malcolm  (2018年04月13日)
		 */
		public function getList($param,$userId,$isIn = false){
			//按距离排序
			$lng = $param['lng'];
			$lat = $param['lat'];
			
			$cityId  = $param['city_id']?$param['city_id']:1388;
			$cityMod  = m("city");
			
			$type = intval($param['type']);
			
			if($type)
				$cond[] = " type = {$type} ";
			
			$order = intval($param['order_id'])?intval($param['order_id']):1;
			
			if($lat && $lng && !$order){
				$fields = 'id,'.Zeus::getDisSql($lat,$lng);
				$orderBy[] = 'distance ASC';
			}else{
				$fields = 'id';
			}
			
			
			
			//按市做筛选
			$cityInfo = [];
			if ( $cityId ) {
				$cityInfo = $cityMod->getInfo($cityId);
			}
			if ( empty($cityInfo) || $cityInfo['is_open'] == 2 ) {
				$cityId = 1388;
			}
			
			//按企业查询
			$companyId = intval($param['company_id']);
			if($companyId)
				$cond[] = " company_id = {$companyId} ";
			else
				$cond[] = " city_id = {$cityId} ";
			
			$cond[] = " is_task = 2 AND status = 1 AND auth_status = 1 AND mark = 1";
			
			
			
			//排除本身
			$not = intval($param['not']);
			if($not)
				$cond[] = " id <> {$not} ";
			
			//关键字
			$keyword = $param['keyword'];
			if($keyword){
				if(is_array($keyword)){
					if ( is_array($keyword) ) {
						$tmp = [];
						foreach ( $keyword as $key => $val ) {
							$tmp[] = " `title` LIKE '%{$val}%'  ";
					    }
					    
					    $tmp = implode(' OR ',$tmp);
					    
						$cond[] = "( {$tmp} )";
					}else{
						$cond[] = " `title` LIKE '%{$keyword}%'  ";
					}
				}
				
			}
			
			
			//行业条件
			$industryId = intval($param['industry_id']);
			if($industryId)
				$cond[] = " FIND_IN_SET('{$industryId}',`industry_id`) ";
			
			//薪资条件
			$money = intval($param['money_id']);
			if($money){
				if(1==$type){   //全职
					switch ($money){
						case 1:
							$cond[] = " money_lower >= 2000 AND money_upper <= 3000 ";
							break;
							
						case 2:
							$cond[] = " money_lower >= 3000 AND money_upper <= 5000 ";
							break;
							
						case 3:
							$cond[] = " money_lower > 5000  ";
							break;
							
						default:
							$cond[] = " money_lower >= 2000 AND money_upper <= 3000 ";
					}
				}else{      //兼职
					switch ($money){
						case 1:
							$cond[] = " money_type = 2 ";
							break;
							
						case 2:
							$cond[] = " money_type = 4 ";
							break;
							
						case 3:
							$cond[] = " money_type = 1 ";
							break;
						case 5:
							$cond[] = " money_type = 5 ";
							break;
					}
				}
			}
			
			//地区条件
			$distId = intval($param['dist_id']);
			if($distId)
				$cond[] = " dist_id = {$distId} ";
			
                        //7.12迭代
                        //发布时间
                        $pubtimeId = intval($param['pubtime_id']);
                        if($pubtimeId){
                            $now = time();
                            $endTime = strtotime(date('Y-m-d 23:59:59',$now));
                            switch ($pubtimeId){
                                //三天内
                                case 1:
                                        $time = strtotime('-2 day', $now);
                                        $beginTime = strtotime(date('Y-m-d 00:00:00', $time));
                                        $cond[] = " add_time >= ".$beginTime." AND add_time <= ".$endTime;
                                        break;
                                //一周内
                                case 2:
                                        $time = strtotime('-6 day', $now);
                                        $beginTime = strtotime(date('Y-m-d 00:00:00', $time));
                                        $cond[] = " add_time >= ".$beginTime." AND add_time <= ".$endTime;
                                        break;
                                //一周以上
                                case 3:
                                        $time = strtotime('-6 day', $now);
                                        $beginTime = strtotime(date('Y-m-d 00:00:00', $time));
                                        $cond[] = " add_time < ".$beginTime;
                                        break;
                            }
                        }
                        //性别要求
			$sexId = intval($param['sex_id'])?:0;
                        if($sexId)
                        $cond[] = "sex = ".$sexId;
                        
                        //上班时间
                        $worktimeId = intval($param['worktime_id']);
			if($worktimeId)
                        $cond[] = "(worktime_id = ".$worktimeId." OR worktime_id = 0)";
                           
                        //距离范围
                        $distanceId = intval($param['distance_id']);
			if($distanceId){
                            switch ($distanceId){
                                //1公里内
                                case 1:
                                    $ar = Zeus::getAround($lat, $lng,1000);
                                    $cond[] = "lat >= ".$ar['lat']['min'] ." AND lat <=".$ar['lat']['max'];
                                    $cond[] = "lng >= ".$ar['lng']['min'] ." AND lng <=".$ar['lng']['max'];
                                    break;
                                //5公里内
                                case 2:
                                    $ar = Zeus::getAround($lat, $lng,5000);
                                    $cond[] = "lat >= ".$ar['lat']['min'] ." AND lat <=".$ar['lat']['max'];
                                    $cond[] = "lng >= ".$ar['lng']['min'] ." AND lng <=".$ar['lng']['max'];
                                    break;
                                //5公里以上
                                case 3:
                                    $ar = Zeus::getAround($lat, $lng,5000);
                                    $cond[] = "lat > ".$ar['lat']['max'];
                                    $cond[] = "lng > ".$ar['lng']['max'];
                                    break;
                            }
                        }
			
			//排序条件
			if($order){
				switch ($order){
					case 1:
						$orderBy[] = '  id DESC ';
						break;
						
					case 2:
						$orderBy[] = '  look_num DESC  ';
						break;
						
					case 3:
						$orderBy[] = '  money_upper DESC ';
						break;
						
					case 4:
						$orderBy[] = '  money_lower ASC';
						break;
						
					default:
						$orderBy[] = '  id DESC ';
				}
			}
			
			
			if(is_array($orderBy))
				$orderBy = implode(',',$orderBy);
			
			$page = $perpage = $limit = 0;
			$this->initPage($page, $perpage, $limit);
			$query = [
				'fields' => $fields,
				'order_by' => $orderBy,
				'cond' => $cond,
				'limit' => $limit,
			];
			$list = $this->mod->getData($query);
			$count = $this->mod->getCount($query['cond']);
			$newList = [];
			
			if ( is_array($list) ) {
				foreach ( $list as $key => $val ) {
                                    $newList[$key] = $this->mod->getListInfo($val['id']);
			    }
			}
			
			$data = [
				'city_id' =>$cityId,
				'count'=>$count,
				'page'=>$page,
				'perpage'=>$perpage,
				'list'=>$newList
			];
			
			if($isIn)
				return $data;
			
			return message('操作成功', true ,$data);
		}
		
		
		/**
		 * @todo    获取职位详情
		 * @author Malcolm  (2018年04月13日)
		 */
		public function getJobInfo($param,$userId){
			$id = intval($param['job_id']);
			if(!$id)
				return message('参数丢失');
			
			$info = $this->mod->getDetailInfo($id);
			if(!$info['job_title'])
				return message('该职位已过期');
			
			//自动维护职位查看次数
			$this->mod->editColumnValue($id,'look_num');
			
			
			//相似职位
			$keyword = Zeus::cutString($info['job_title']);
			if(!$keyword)
				$keyword = $info['job_title'];
			
			
			$likeList = $this->getList([
				'type' => $info['job_type'],
				'city_id' => $param['city_id'],
				'not' => $id,
				'keyword' => $keyword,
			],$userId,true);
			
			$info['like_job_list'] = $likeList['list'];
			
			//是否已申请 已收藏
			if($userId){
				$cond = " is_task = 2 AND job_id = {$id} AND user_id = {$userId}  AND status < 4 AND mark = 1";
				$count = m('jobApply')->getCount($cond);
				
				$info['is_apply'] = $count?1:2;
				
				$cond = " type = {$info['job_type']} AND type_id = {$id} AND user_id = {$userId} AND mark = 1 ";
				$count = m('collect')->getCount($cond);
				
				$info['is_collect'] = $count?1:2;
				$info['collect_id'] = 0;
				
				if($info['is_collect']==1){
					$collectId = m('collect')->getIds($cond);
					$info['collect_id'] = $collectId[0];
				}
				
				
			}else{
				$info['is_apply'] =$info['is_collect'] = 2;
				$info['collect_id'] = 0;
			}
			
			
			return message('操作成功', true ,$info);
		}
		
		
		
		/**
		 * @todo    投递简历
		 * @author Malcolm  (2018年04月14日)
		 */
		public function setJobApply($param,$userId){
			$jobId = intval($param['job_id']);
			
			if(!$jobId)
				return message('参数丢失');


			//判断用户身份
            $userType = m('user')->getUserType($userId);
            if($userType != 1)
                return message('仅普通用户可投递简历');
			
			$jobApplyMod = m('jobApply');
			
			$cond = " job_id = {$jobId} AND user_id = {$userId} AND status < 4 AND mark = 1 ";
			$count = $jobApplyMod->getCount($cond);
			
			if($count)
				return message( '您已投递，无法重复投递' );
			
			
			//获取简历详情
			$resumeInfo = m('resume')->getInfoByUserId($userId);
			if(!$resumeInfo['name'] || !$resumeInfo['birthday'] || !$resumeInfo['address']|| !$resumeInfo['tel'])
				return message( '请先完善简历信息后再投递' );
			
			$jobInfo = $this->mod->getInfo($jobId);
			
			$data = [
				'type' => $jobInfo['type'],
				'is_task' => $jobInfo['is_task'],
				'job_id' => $jobId,
				'company_id' => $jobInfo['company_id'],
				'user_id' => $userId,
				'status' => 1,
				'snap' => serialize($jobInfo),
			];
			
			$rs = $jobApplyMod->edit($data);
			if(!$rs)
				return message('系统繁忙，请稍候再试');
			
			
			//发送通知
			Zeus::sendMsg([
				'type' =>['msg','push'],
				'user_id' =>$userId,
				'title' =>'成功投递简历',
				'content' =>"您已成功投递职位【{$jobInfo['title']}】,我们会尽快为你审核处理",
				'msg_type' =>1,
				'user_type' =>1,
			]);
			
			return message('操作成功', true );
		}
		
		
		/**
		 * @todo    获取企业信息
		 * @author Malcolm  (2018年04月14日)
		 */
		public function getCompanyInfo($param,$userId){
			$companyId = intval($param['company_id']);
			if(!$companyId)
				return message('参数丢失');
			
			$companyInfo = m('company')->getDetailInfo($companyId);
			
			
			$companyList = $this->getList($param,$userId,true);
			
			$data = [
				'company_info' =>$companyInfo,
				'company_job_list' =>$companyList,
			];
			
			return message('操作成功', true ,$data);
		}
		
		
		
	}