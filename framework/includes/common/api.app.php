<?php
	
	class ApiApp extends BaseApp {
		
		/**请求的参数*/
		protected $req , $reqId , $post;
		/** 当前登录的用户编号*/
		protected $userId;
		/** 当前用户基本信息*/
		protected $userInfo;
		
		protected $pageMod;
		
		protected $errorFlag = false;
		
		public function __construct ( $check = true ) {
			parent::__construct();
			
			$this->userId = 0;
			log::setMode(log::MODE_INPUT | log::MODE_DB | log::MODE_OUTPUT);
			$this->initRequest();
		}
		
		protected function initRequest () {
			$reqId       = substr(md5(time() . Zeus::getRandCode(10) . rand(1 , 1000)) , 8 , 16);
			$this->reqId = $reqId;
			
			//预加载配置
			inc("msg.inc");
			$this->req  = $_REQUEST;
			$this->post = $_POST;
			$userId     = $this->req['user_id'];
			if ( log::hasMode(log::MODE_INPUT) ) {
				$reqId = $this->reqId;
				log::jsonInfo($reqId , "request_id");
				log::jsonInfo($this->req , "input");
			}
			//校验登录信息
			if ( $userId ) {
				$token    = trim($this->req['token']);
				$userMod  = m("user");
				$userInfo = $userMod->getInfo($userId);
				if ( empty($token) || ( $userInfo['token'] != $token ) ) {
					log::jsonInfo($userInfo);
					log::jsonInfo("info token {$userInfo['token']} != {$token}");
					$this->jsonReturn(MESSAGE_NEEDLOGIN , false , [
						'is_need_jump_login' => 1
					]);
					
					if ( $userInfo['status'] == 4 ) {
						$this->jsonReturn('您的帐号已被停用！');
					}
					
				}
				$this->userId   = $userId;
				$this->userInfo = $userInfo;
			}
			if ( log::hasMode(log::MODE_INPUT) ) {
				log::jsonInfo($this->userInfo['mobile'] , "mobile");
			}
		}
		
		
		/**
		 * @todo    监测登录状态
		 * @author  Malcolm  (2018年02月05日)
		 */
		protected function needLogin () {
			if ( !$this->userId ) {
				$this->jsonReturn(MESSAGE_NEEDLOGIN , false , [
					'is_need_jump_login' => 1
				]);
			}
		}
		
		/**
		 * @todo    监测审核
		 * @author  Malcolm  (2017年12月28日)
		 */
		protected function needAuth () {
			$userInfo = $this->userInfo;
			if ( $userInfo['status'] == 2 || $userInfo['status'] == 3 ) {
				$this->jsonReturn('您的帐号尚未通过实名审核，无法进行此操作！');
			}
			
			if ( $userInfo['status'] == 4 ) {
				$this->jsonReturn('您的帐号已被停用！');
			}
		}
		
		/**
		 * 输出JSON数据
		 * @author 刘小祥
		 * @date   2016年3月8日
		 */
		protected function jsonReturn () {
			false && message();
			$arr = func_get_args();
			//兼容调用message (messsageEx)的返回结果
			if ( !is_array($arr[0]) ) {
				$result = call_user_func_array("message" , $arr);
			} else {
				$result = $arr[0];
			}
			$code = $result[0];
			//格式化数组
			$result = $this->getStringArray($result);
			
			$result['request_id'] = $this->reqId;

			//加密成字符串输出
			if ( log::hasMode(log::MODE_OUTPUT) ) {
				log::jsonInfo($result , "output");
			}

            if($_REQUEST['is_debug'])
                printd($result);

            $output = json_encode($result);

            if($_REQUEST['noAes'] ==1){
                echo $output;
                exit();
            }



			$crypt  = getCryptDesObject();
			$output = $crypt->encrypt($output);
			is_debug() && $output = $crypt->decrypt($output);
			echo $output;
			exit();
		}
		
		/**
		 * 格式化数组key
		 * @author 刘小祥 (2016年5月17日)
		 * @param array 要格式化的数组
		 * @return array 格式化后的数组
		 */
		protected function getIndexArray ( $array ) {
			$index = 0;
			$data  = [];
			foreach ( $array as $key => $row ) {
				if ( is_array($row) ) {
					$row = $this->getIndexArray($row);
				}
				if ( is_numeric($key) ) {
					$data[$index] = $row;
					$index++;
				} else {
					$data[$key] = $row;
				}
			}
			
			return $data;
		}
		
		/**
		 * 格式化为全字符串的数组
		 * @author 刘小祥
		 * @date   2016年4月9日
		 */
		private function getStringArray ( $array ) {
			foreach ( $array as $key => $row ) {
				if ( is_array($row) ) {
					$array[$key] = $this->getStringArray($row);
				} elseif ( is_object($row) ) {
					// do nothing
				} else {
					$array[$key] = (string)$row;
				}
			}
			
			return $array;
		}
		
		/**
		 * 分页初始化
		 * @author 刘小祥 (2016年9月6日)
		 */
		protected function initPage ( &$page , &$perpage , &$limit ) {
			$page       = (int)$this->req['page'];
			$perpage    = (int)$this->req['perpage'];
			$page       = $page ? $page : 1;
			$perpage    = $perpage ? $perpage : 10;
			$startIndex = ( $page - 1 ) * $perpage;
			$limit      = "{$startIndex}, {$perpage}";
		}
		
		/**
		 * @todo   获取分页数据
		 * @author 刘小祥 (2017年9月20日)
		 */
		protected function pageData ( $query ) {
			$page = $perpage = $limit = null;
			$this->initPage($page , $perpage , $limit);
			if ( is_string($query) ) {
				$query = [
					'cond' => $query ,
				];
			}
			if ( !isset($query['fields']) ) {
				$query['fields'] = [ 'getShortInfo' ];
			}
			if ( !isset($query['order_by']) ) {
				$query['order_by'] = "id DESC";
			}
			$query['limit'] = $limit;
			$count          = $this->pageMod->getCount($query['cond']);
			$list           = $this->pageMod->getData($query);
			
			return [
				'page'    => $page ,
				'perpage' => $perpage ,
				'count'   => $count ,
				'list'    => $list ,
			];
			
		}
		
		/**
		 * 析构方法
		 * @author 刘小祥 (2016年5月7日)
		 */
		public function __destruct () {
			//fastcgi模式下可以提前结束响应
			if ( function_exists("fastcgi_finish_request") ) {
				fastcgi_finish_request();
			}
			//写入日志
			$name = "main";
			log::jsonInfo(runtime() , "runtime");
			log::jsonInfo($name . $this->req['version'] , "version"); //用户版
			log::jsonWrite("njyb_log");
		}
		
		public function index () {
			$this->jsonReturn('操作成功' , true , [
				'req' => $_REQUEST ,
			]);
		}
		
		public function do_action ( $action ) {
			if ( $action && $action{0} != '_' && method_exists($this , $action) ) {
				$this->_curr_action = $action;
				$this->_run_action(); //运行动作
			} else {
				$this->jsonReturn('app或者act参数错误' , false , [
					'req' => $_REQUEST ,
				]);
			}
		}
	}
