<?php
	
	/**
	 * 业务逻辑基类
	 * @author 刘小祥
	 * @date   2016年3月11日
	 */
	class Zeus {
		
		public $mod;
		
		public function __construct ( $mod ) {
			$mod && $this->mod = m($mod);
		}
		
		
		public static function isValidMobile ( $mobile ) {
			return preg_match('/^1[345789]{1}\d{9}$/' , $mobile) ? true : false;
		}
		
		public static function isValidZipCode ( $code ) {
			return preg_match('/^[1-9][0-9]{5}$/' , $code) ? true : false;
		}
		
		public static function isValidEmail ( $email ) {
			$checkmail = "/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/"; //定义正则表达式
			
			return preg_match($checkmail , $email) ? true : false;
		}
		
		/**
		 * 生成随机字符
		 * @author 刘小祥
		 * @param int $num code的长度
		 * @date   2016年3月16日
		 * @return string
		 */
		static function getRandCode ( $num = 12 ) {
			$codeSeeds = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			$codeSeeds .= "abcdefghijklmnopqrstuvwxyz";
			$codeSeeds .= "0123456789_";
			$len       = strlen($codeSeeds);
			$code      = "";
			for ( $i = 0 ; $i < $num ; $i++ ) {
				$rand = rand(0 , $len - 1);
				$code .= $codeSeeds[$rand];
			}
			
			return $code;
		}
		
		
		/**
		 * @todo    生成字母和数字组合的密码
		 * @author  zhouquan <zhouquan@quyishu.com>
		 * @date    : 2016年8月9日
		 * @param int $letterNum 字母个数
		 * @param int $numberNum 数字个数
		 * @return string
		 */
		public static function getUserPassword ( $letterNum = 1 , $numberNum = 5 ) {
			//获取随机的字母
			$letterSeeds = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			
			$letterSeeds .= "abcdefghijklmnopqrstuvwxyz";
			
			$len = strlen($letterSeeds);
			
			$code = '';
			
			for ( $i = 0 ; $i < $letterNum ; $i++ ) {
				$rand = rand(0 , $len - 1);
				
				$code .= $letterSeeds[$rand];
			}
			
			//获取随机的数字
			$numSeeds = "0123456789";
			
			$len = strlen($numSeeds);
			
			for ( $i = 0 ; $i < $numberNum ; $i++ ) {
				$rand = rand(0 , $len - 1);
				
				$code .= $numSeeds[$rand];
			}
			
			return $code;
		}
		
		
		public static function isValidPassword ( $password , $isLength = 2 ) {
			if ( strlen($password) > 12 || strlen($password) < 6 ) {
				return false;
			}
			
			if ( 2 == $isLength ) {
				if ( preg_match("/^\d*$/" , $password) ) {
					return false; //全数字
				}
				if ( preg_match("/^[a-z]*$/i" , $password) ) {
					return false; //全字母
				}
				if ( !preg_match("/^[a-z\d]*$/i" , $password) ) {
					return false; //特殊字符;
				}
			}
			
			return true;
		}
		
		
		/**
		 * @todo    获取百分比
		 * @author  Malcolm  (2017年10月11日)
		 */
		public static function getPercentum ( $son , $mother , $isUnit = false ) {
			$num = number_format($son / $mother , 2 , "." , "") * 100;
			
			if ( $isUnit ) {
				$num .= '%';
			}
			
			return $num;
		}
		
		
		/**
		 * 检查身份证号是否合法
		 * @param string $id 身份证号
		 * @return bool 合法返回TRUE 否则FALSE
		 * @author 刘小祥 (2016年10月12日)
		 */
		public static function isValidIdNo ( $id ) {
			$id        = strtoupper($id);
			$regx      = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
			$arr_split = [];
			if ( !preg_match($regx , $id) ) {
				return false;
			}
			//检查15位
			if ( 15 == strlen($id) ) {
				$regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";
				@preg_match($regx , $id , $arr_split);
				$dtm_birth = "19" . $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
				if ( !strtotime($dtm_birth) ) {
					return false;
				} else {
					return true;
				}
			} else {
				//检查18位
				$regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
				@preg_match($regx , $id , $arr_split);
				$dtm_birth = $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
				//检查生日日期是否正确
				if ( !strtotime($dtm_birth) ) {
					return false;
				} else {
					//检验18位身份证的校验码是否正确。
					//校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
					$arr_int = [ 7 , 9 , 10 , 5 , 8 , 4 , 2 , 1 , 6 , 3 , 7 , 9 , 10 , 5 , 8 , 4 , 2 ];
					$arr_ch  = [ '1' , '0' , 'X' , '9' , '8' , '7' , '6' , '5' , '4' , '3' , '2' ];
					$sign    = 0;
					for ( $i = 0 ; $i < 17 ; $i++ ) {
						$b    = (int)$id{$i};
						$w    = $arr_int[$i];
						$sign += $b * $w;
					}
					$n       = $sign % 11;
					$val_num = $arr_ch[$n];
					if ( $val_num != substr($id , 17 , 1) ) {
						return false;
					} else {
						return true;
					}
				}
			}
		}
		
		/**
		 * @todo    根据日期获取随机数
		 * @author  Malcolm  (2017年05月22日)
		 */
		public static function getCodeByDate () {
			$time = time();
			$m    = date("m" , $time);
			$d    = date("d" , $time);
			$h    = date("H" , $time);
			$i    = date("i" , $time);
			$s    = date("s" , $time);
			
			$rand = rand(1000 , 9999);
			
			return $m . $d . $h . $i . $s . $rand;
		}
		
		/**
		 * @todo    发送短信
		 * @author  Malcolm  (2017年12月20日)
		 */
		public static function sendSms ( $mobile , $content ) {
			import('sms.class');
			Sms::ningWangSend($mobile , $content);
			
			return true;
		}
		
		
		/**
		 * @todo    获取分词（提取关键词）
		 * @author Malcolm  (2018年04月13日)
		 * @param string $str   需要分词的字符串
		 * @param string $charset  字符串的字符集  utf8   gbk
		 * @return bool array
		 */
		public static function cutString($str,$charset='utf8'){
			$rs = Zeus::getServiceResponse([
				'app'=>"cutString",
				'act'=>"handel",
				'string'=>$str,
				'charset'=>$charset,
			]);
			
			if(!$rs['success'])
				return false;
			
			
			return $rs['data'];
		}
		
		
		
		/**
		 * @todo    系统服务
		 * @author Malcolm  (2018年03月30日)
		 */
		static function getServiceResponse($data=array(), $url = SERVICE_URL) {
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			if ($data) {
				$crypt = getCryptDesObject();
				$data = $crypt->encrypt(json_encode($data));
				
				$data = http_build_query(array('APIDATA'=>$data), null, "&");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			}
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			$responseText = curl_exec($ch);
			//log::jsonInfo($responseText);
			$responseText = $crypt->decrypt($responseText, API_KEY);
			//log::jsonInfo($responseText);
			$info = json_decode($responseText, 1);
			if (!$info) {
				$info = array();
				$info['code']= "90001";
				$info['msg'] = "网络异常:".curl_error($ch);
				//log::jsonInfo($info);
			}
			
			return $info;
		}
		
		
		/**
		 * @todo    发送短信验证码
		 * @author  Malcolm  (2018年01月31日)
		 */
		public static function sendSignSms ( $mobile ) {
			Common::send_sign($mobile);
			
			return true;
		}
		
		
		/**
		 * @todo    发送消息
		 * @author  Malcolm  (2017年09月22日)
		 */
		public static function sendMsg ( $data ) {
			//消息类型   短信 sms  推送 push 3消息msg
			$type = $data['type'];
			
			$mobile = $data['mobile'];
			
			$content = $data['content'];
			
			if ( !$mobile ) {
				$userId = $data['user_id'];
				if ( !$userId ) {
					return false;
				}
				
				$userInfo       = m('user')->getInfo($userId);
				$mobile         = $userInfo['mobile'];
				$data['mobile'] = $userInfo['mobile'];
			}
			
			if ( !$type || !$mobile || !$content ) {
				return false;
			}
			
			//加入队列
			
			Zeus::push('message' , $data);
			
			
			return true;
		}
		
		/**
		 * @todo   加入消息队列
		 * @author Malcolm (公元前2017年12月21日)
		 */
		public static function push ( $name , $data ) {
			import('/rabbitMq/rabbitMQ');
			$queue = new RabbitMq($name);
			$queue->put($data);
			
			return true;
		}
		
		/**
		 * @todo    设置队列日志
		 * @author  Malcolm  (2017年08月18日)
		 * @param int    $type   类型   1发送 2消费
		 * @param int    $status 状态 1成功 2失败
		 * @param string $log    日志内容
		 * @return bool
		 */
		public static function setRabbitMQLog ( $type = 1 , $status = 1 , $log , $queueName = '' ) {
			$name = 'sendLog_success';
			
			if ( 1 == $type ) {
				if ( 1 == $status ) {
					$name = 'sendLog_success';
				} else {
					$name = 'sendLog_error';
				}
				
			}
			
			if ( 2 == $type ) {
				if ( 1 == $status ) {
					$name = 'consumeLog_success';
				} else {
					$name = 'consumeLog_error';
				}
				
			}
			
			if ( $queueName ) {
				$name = $queueName . '_' . $name;
			}
			
			$log .= "时间：" . date('Y-m-d H:i:s' , time()) . "\n";
			
			$dir = LOG_PATH . '/rabbitMQLog';
			
			if ( !is_dir($dir) ) {
				mkdir($dir , 0777 , true);
			}
			
			//写入日志
			file_put_contents($dir . '/' . $name . '.log' , $log , FILE_APPEND);
			
			//写入redis队列
			$redis = cache_server()->server;
			$redis->lpush($name , $log);
			
			return true;
		}
		
		/**
		 * 分页初始化
		 * @author 刘小祥 (2016年9月6日)
		 */
		public static function initPage ( &$page , &$perpage , &$limit ) {
			$page       = (int)$_REQUEST['page'];
			$perpage    = (int)$_REQUEST['perpage'];
			$page       = $page ? $page : 1;
			$perpage    = $perpage ? $perpage : 10;
			$startIndex = ( $page - 1 ) * $perpage;
			$limit      = "{$startIndex}, {$perpage}";
		}
		
		/**
		 * @todo    API接口 获取分页数据
		 * @author  Malcolm  (2017年10月10日)
		 */
		public static function pageData ( $query , $mod , $fields = 'getShortInfo' , $num = 0 ) {
			$page = $perpage = $limit = null;
			
			if ( $num ) {
				$_REQUEST['perpage'] = $num;
			}
			
			if ( defined('IS_API') ) {    //API 分页处理
				self::initPage($page , $perpage , $limit);
				$query['limit'] = $limit;
			} else {  //layui分页处理
				$pageIndex = I('page' , 1);
				
				$query['limit'] = ( ( $pageIndex - 1 ) * I('limit' , 10) ) . ',' . I('limit' , 10);
			}
			
			
			if ( is_string($query) ) {
				$query = [
					'cond' => $query ,
				];
			}
			
			
			if ( !isset($query['fields']) ) {
				$query['fields'] = [ $fields ];
			}
			if ( !isset($query['order_by']) ) {
				$query['order_by'] = "id DESC";
			}
			
			
			if ( !is_object($mod) ) {
				$mod = m($mod);
			}
			
			$count = $mod->getCount($query['cond']);
			$list  = $mod->getData($query);
			
			if ( !$list ) {
				$list = [];
			}
			
			if ( defined('IS_API') ) {    //API 分页返回
				return [
					'page'    => $page ,
					'perpage' => $perpage ,
					'count'   => $count ,
					'list'    => $list ,
				];
			} else {  //前台分页返回
				$res = [
					'code'   => 0 ,
					'status' => true ,
					'msg'    => '' ,
					'count'  => $count ,
					'data'   => $list
				];
				
				return $res;
			}
			
		}
		
		/**
		 * @todo    银行卡号格式化
		 * @author  Malcolm  (2017年10月11日)
		 */
		public static function formatBankCardNo ( $bankCardNo , $isCover = true ) {
			if ( $isCover ) {
				//截取银行卡号前4位
				$prefix = substr($bankCardNo , 0 , 4);
				//截取银行卡号后4位
				$suffix = substr($bankCardNo , -4 , 4);
				
				$maskBankCardNo = $prefix . " **** **** **** " . $suffix;
			} else {
				$arr            = str_split($bankCardNo , 4); //4的意思就是每4个为一组
				$maskBankCardNo = implode(' ' , $arr);
			}
			
			return $maskBankCardNo;
		}
		
		/**
		 * @todo   获取配置数据
		 * @author 刘小祥 (2017年10月14日)
		 */
		static function config ( $tag , $key = null ) {
			if ( !isset($GLOBALS['config_list']) ) {
				$configMod              = m("config");
				$GLOBALS['config_list'] = $configMod->getData([
					'pri'    => "tag" ,
					'fields' => "tag,content"
				]);
			}
			$data = unserialize($GLOBALS['config_list'][$tag]['content']);
			if ( $key !== null ) {
				return $data[$key];
			}
			
			return $data;
		}
		
		/**
		 * @todo    数字转大写金额
		 * @author  Malcolm  (2017年11月06日)
		 * @param int    $number        数值
		 * @param string $int_unit      币种单位，默认"元"或者"圆"
		 * @param bool   $is_round      是否对小数进行四舍五入
		 * @param bool   $is_extra_zero 是否对整数部分以0结尾，小数存在的数字附加0,比如1960.30  输出"壹仟玖佰陆拾元零叁角"
		 * @return string
		 */
		public static function num2rmb ( $number = 0 , $int_unit = '元' , $is_round = true , $is_extra_zero = false ) {
			// 将数字切分成两段
			$parts = explode('.' , $number , 2);
			$int   = isset($parts[0]) ? strval($parts[0]) : '0';
			$dec   = isset($parts[1]) ? strval($parts[1]) : '';
			
			// 如果小数点后多于2位，不四舍五入就直接截，否则就处理
			$dec_len = strlen($dec);
			if ( isset($parts[1]) && $dec_len > 2 ) {
				$dec = $is_round ? substr(strrchr(strval(round(floatval("0." . $dec) , 2)) , '.') , 1) : substr($parts[1] , 0 , 2);
			}
			
			// 当number为0.001时，小数点后的金额为0元
			if ( empty($int) && empty($dec) ) {
				return '零';
			}
			
			// 定义
			$chs     = [ '0' , '壹' , '贰' , '叁' , '肆' , '伍' , '陆' , '柒' , '捌' , '玖' ];
			$uni     = [ '' , '拾' , '佰' , '仟' ];
			$dec_uni = [ '角' , '分' ];
			$exp     = [ '' , '万' ];
			$res     = '';
			
			// 整数部分从右向左找
			for ( $i = strlen($int) - 1 , $k = 0 ; $i >= 0 ; $k++ ) {
				$str = '';
				// 按照中文读写习惯，每4个字为一段进行转化，i一直在减
				for ( $j = 0 ; $j < 4 && $i >= 0 ; $j++ , $i-- ) {
					$u   = $int{$i} > 0 ? $uni[$j] : ''; // 非0的数字后面添加单位
					$str = $chs[$int{$i}] . $u . $str;
				}
				//echo $str."|".($k - 2)."<br>";
				$str = rtrim($str , '0'); // 去掉末尾的0
				$str = preg_replace("/0+/" , "零" , $str); // 替换多个连续的0
				if ( !isset($exp[$k]) ) {
					$exp[$k] = $exp[$k - 2] . '亿'; // 构建单位
				}
				$u2  = $str != '' ? $exp[$k] : '';
				$res = $str . $u2 . $res;
			}
			
			// 如果小数部分处理完之后是00，需要处理下
			$dec = rtrim($dec , '0');
			
			// 小数部分从左向右找
			if ( !empty($dec) ) {
				$res .= $int_unit;
				
				// 是否要在整数部分以0结尾的数字后附加0，有的系统有这要求
				if ( $is_extra_zero ) {
					if ( substr($int , -1) === '0' ) {
						$res .= '零';
					}
				}
				
				for ( $i = 0 , $cnt = strlen($dec) ; $i < $cnt ; $i++ ) {
					$u   = $dec{$i} > 0 ? $dec_uni[$i] : ''; // 非0的数字后面添加单位
					$res .= $chs[$dec{$i}] . $u;
				}
				$res = rtrim($res , '0'); // 去掉末尾的0
				$res = preg_replace("/0+/" , "零" , $res); // 替换多个连续的0
			} else {
				$res .= $int_unit . '整';
			}
			
			return $res;
		}
		
		/**
		 * @todo    过滤表单数据  去除大部分注入
		 * @author  Malcolm  (2017年11月17日)
		 */
		public static function filterFromData ( $str ) {
			$preg = "/<script[\s\S]*?<\/script>/i";
			$str  = preg_replace($preg , "" , $str);
			$str  = preg_replace("/<a[^>]*>(.*)<\/a>/isU" , '${1}' , $str);
			
			return $str;
		}
		
		/**
		 * @todo    去除HTML标签、图像等 仅保留文本
		 * @author  Malcolm  (2017年05月30日)
		 */
		public static function getStrByHtml ( $str , $is_sub = 2 ) {
			$str  = htmlspecialchars_decode($str); //把一些预定义的 HTML 实体转换为字符
			$str  = str_replace("&nbsp;" , "" , $str); //将空格替换成空
			$str  = strip_tags($str); //函数剥去字符串中的 HTML、XML 以及 PHP 的标签,获取纯文本内容
			$str  = str_replace([ "\n" , "\r\n" , "\r" ] , ' ' , $str);
			$preg = "/<script[\s\S]*?<\/script>/i";
			$str  = preg_replace($preg , "" , $str , -1); //剥离JS代码
			
			if ( 2 == $is_sub ) {
				$str = mb_substr($str , 0 , 100 , "utf-8");
			}
			
			//返回字符串中的前100字符串长度的字符
			
			return $str;
		}
		
		/**
		 * @todo    去除富文本样式属性(自动图片本地化)
		 * @author  Malcolm  (2017年05月04日)
		 */
		public static function dislodgeStyleFromHtml ( &$content ) {
			$content = str_replace("\\" , '' , $content);
			$content = preg_replace("/<p([^>]+?)>/" , "<p>" , $content);
			
			$content = preg_replace("/<img[^>]+?(src=['\"].+?['\"])[^>]*?>/" , "<img $1 >" , $content);
			
			$content = trim($content);
			
			//Zeus::saveImageByContent($content);
		}
		
		
		/**
		 * @todo    简单格式化富文本
		 * @author Malcolm  (2018年04月24日)
		 */
		public static function saveFormatHtml(&$content){
			$content = str_replace("\\" , '' , $content);
			
			//去除img标签原有属性 增加宽度限制
			$content = preg_replace("/<img[^>]+?(src=['\"].+?['\"])[^>]*?>/" , "<img $1 style='max-width: 90%'>" , $content);
			
			$content = trim($content);
		}
		
		
		/**
		 * @todo    生成密码
		 * @author  Malcolm  (2017年05月09日)
		 */
		public static function getPassWord ( $password ) {
			return md5(md5($password));
		}
		
		/**
		 * @todo    获取客户端IP
		 * @author  Malcolm  (2017年11月17日)
		 */
		public static function getClientIP () {
			$ip = '';
			if ( getenv("HTTP_CLIENT_IP") ) {
				$ip = getenv("HTTP_CLIENT_IP");
			} elseif ( getenv("HTTP_X_FORWARDED_FOR") ) {
				$ip = getenv("HTTP_X_FORWARDED_FOR");
			} elseif ( getenv("REMOTE_ADDR") ) {
				$ip = getenv("REMOTE_ADDR");
			}
			
			return $ip;
		}
		
		/**
		 * @todo    设置登录日志
		 * @author  Malcolm  (2017年11月17日)
		 */
		public static function setLoginRecord ( $userId , $type = 1 ) {
			$data = [
				'type'       => $type ,
				'type_id'    => $userId ,
				'ip_address' => self::getClientIP() ,
			];
			
			//加入队列
			import('/rabbitMq/rabbitMQ');
			//规定路由名称
			$queue = new RabbitMq('setLoginRecord');
			
			$queue->put($data);
			
			return true;
		}
		
		
		/**
		 * @todo    获取邀请码
		 * @author  Malcolm  (2017年12月20日)
		 */
		public static function getCodeById ( $id ) {
			$time  = md5(time());
			$left  = substr($time , 0 , 2);
			$right = substr($time , -2);
			
			return $left . base_convert(800000 + $id , 10 , 36) . $right;
		}
		
		/**
		 * @todo    根据邀请码获取ID
		 * @author  Malcolm  (2017年12月20日)
		 */
		public static function getIdByCode ( $code ) {
			$str = substr($code , 2 , 4);
			$id  = base_convert($str , 36 , 10);
			
			return $id - 800000;
		}
		
		
		/**
		 * @todo    获取VIP等级
		 * @author  Malcolm  (2018年03月09日)
		 */
		public static function getVipLevel ( $num ) {
			$levelArr = [
				1000 , 3000 , 6000 , 9000 , 12000 , 15000 , 18000
			];
			
			if ( $num < 1000 )
				return 1;
			
			if ( $num > 18000 )
				return 7;
			
			if ( is_array($levelArr) ) {
				foreach ( $levelArr as $key => $val ) {
					if ( $num <= $val && $num > $levelArr[$key - 1] )
						return $key;
				}
			}
			
		}
		
		
		/**
		 * @todo    获取距离排序SQL语句
		 * @author  Malcolm  (2018年04月13日)
		 */
		public static function getDisSql ( $lat , $lng , $asName = 'distance' ) {
			return " (2 * 6378.137* ASIN(SQRT(POW(SIN(3.1415926535898*({$lat}-lat)/360),2)+COS(3.1415926535898*{$lat}/180)* COS(lat * 3.1415926535898/180)*POW(SIN(3.1415926535898*({$lng}-lng)/360),2))))*1 as {$asName} ";
		}
		
		
		/**
		 * @todo    获取经纬度范围
		 * @author wangqs  (2017年12月19日)
		 * @param $lat  int 纬度
		 * @param $lon  int 精度
		 * @param $raidus  string  范围  单位米
		 * @return  array
		 */
		static public function getAround($lat,$lon,$raidus){
			$PI = 3.14159265;
			
			$latitude = $lat;
			$longitude = $lon;
			
			$degree = (24901*1609)/360.0;
			$raidusMile = $raidus;
			
			$dpmLat = 1/$degree;
			$radiusLat = $dpmLat*$raidusMile;
			$minLat = $latitude - $radiusLat;
			$maxLat = $latitude + $radiusLat;
			
			$mpdLng = $degree*cos($latitude * ($PI/180));
			$dpmLng = 1 / $mpdLng;
			$radiusLng = $dpmLng*$raidusMile;
			$minLng = $longitude - $radiusLng;
			$maxLng = $longitude + $radiusLng;
			
			return [
				'lat'=>[
					'min'=>$minLat,
					'max'=>$maxLat,
				],
				
				'lng'=>[
					'min'=>$minLng,
					'max'=>$maxLng,
				],
			];
		}


        /**
         * @todo    生成临时路径
         * @author Malcolm  (2018年06月20日）
         */
        static function createImagePath($prefix="", $ext="", $root='/tmp') {
            $dateDir = date("/Y/m/d/H");
            if ($dateDir) {
                $dateDir = ($prefix ? "/" : '') .$prefix.$dateDir;
            }
            if (!$ext) $ext = "jpg";
            $absImgPath = $root.$dateDir;
            if (!is_dir($absImgPath)) mkdir($absImgPath, 0777, true);
            $filename = substr(md5(time().rand(0,999999)),8, 16).rand(100,999).".{$ext}";
            $filePath = $dateDir."/".$filename;
            return $filePath;
        }


		/**
		 * @todo    获取首字母
		 * @author Malcolm  (2018年07月10日)
		 */
		public static function getFirstCharter($str){
			if(empty($str)){return '';}
			$fchar=ord($str{0});
			if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
			$s1=iconv('UTF-8','gb2312',$str);
			$s2=iconv('gb2312','UTF-8',$s1);
			$s=$s2==$str?$s1:$str;
			$asc=ord($s{0})*256+ord($s{1})-65536;
			if($asc>=-20319&&$asc<=-20284) return 'A';
			if($asc>=-20283&&$asc<=-19776) return 'B';
			if($asc>=-19775&&$asc<=-19219) return 'C';
			if($asc>=-19218&&$asc<=-18711) return 'D';
			if($asc>=-18710&&$asc<=-18527) return 'E';
			if($asc>=-18526&&$asc<=-18240) return 'F';
			if($asc>=-18239&&$asc<=-17923) return 'G';
			if($asc>=-17922&&$asc<=-17418) return 'H';
			if($asc>=-17417&&$asc<=-16475) return 'J';
			if($asc>=-16474&&$asc<=-16213) return 'K';
			if($asc>=-16212&&$asc<=-15641) return 'L';
			if($asc>=-15640&&$asc<=-15166) return 'M';
			if($asc>=-15165&&$asc<=-14923) return 'N';
			if($asc>=-14922&&$asc<=-14915) return 'O';
			if($asc>=-14914&&$asc<=-14631) return 'P';
			if($asc>=-14630&&$asc<=-14150) return 'Q';
			if($asc>=-14149&&$asc<=-14091) return 'R';
			if($asc>=-14090&&$asc<=-13319) return 'S';
			if($asc>=-13318&&$asc<=-12839) return 'T';
			if($asc>=-12838&&$asc<=-12557) return 'W';
			if($asc>=-12556&&$asc<=-11848) return 'X';
			if($asc>=-11847&&$asc<=-11056) return 'Y';
			if($asc>=-11055&&$asc<=-10247) return 'Z';
			return null;
		}

	}
