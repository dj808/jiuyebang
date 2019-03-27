<?php
	
	/**
	 * 前台常用封装
	 * @author 刘小祥
	 * @date   2016年3月24日
	 */
	class Hera {
		
		public static function getSereviceResponse ( $data = [] , $url = SERVICE_URL ) {
			$ch = curl_init($url);
			curl_setopt($ch , CURLOPT_POST , true);
			if ( $data ) {
				$crypt = getCryptDesObject();
				$data  = $crypt->encrypt(json_encode($data));
				$data  = http_build_query([ 'APIDATA' => $data ] , null , "&");
				curl_setopt($ch , CURLOPT_POSTFIELDS , $data);
			}
			curl_setopt($ch , CURLOPT_RETURNTRANSFER , true);
			$responseText = curl_exec($ch);
			$responseText = $crypt->decrypt($responseText , API_KEY);
			$info         = json_decode($responseText , 1);
			if ( !$info ) {
				$info         = [];
				$info['code'] = "90001";
				$info['msg']  = "网络异常:" . curl_error($ch);
			}
			
			return $info;
		}
		
		/**
		 * 友好的描述发布时间
		 * @author 刘小祥
		 * @date   2016年3月30日
		 * @param int $time 时间戳
		 * @return string
		 */
		public static function humanDate ( $time = null ) {
			$text = '';
			$time = $time === null || $time > time() ? time() : intval($time);
			$t    = time() - $time; //时间差 （秒）
			$y    = date('Y' , $time) - date('Y' , time()); //是否跨年
			$w   = date('W' , $time) - date('W' , time()); //是否跨周
			
			switch ($t) {
				case 0:
					$text = '刚刚';
					break;
				case $t < 60:
					$text = $t . '秒前'; // 一分钟内
					break;
				case $t < 60 * 60:
					$text = floor($t / 60) . '分钟前'; //一小时内
					break;
				case $t < 60 * 60 * 24:
					$text = floor($t / ( 60 * 60 )) . '小时前'; // 一天内
					break;
				case $t < 60 * 60 * 24 * 7&& $w==0:
					$weekday = [ '周日' , '周一' , '周二' , '周三' , '周四' , '周五' , '周六' ];
					$text    = $weekday[date('w' , $time)]; //一周内（不跨周）
					break;
				
				case $t < 60 * 60 * 24 * 7&& $w != 0:
					$weekday = [ '上周日' , '上周一' , '上周二' , '上周三' , '上周四' , '上周五' , '上周六' ];
					$text    = $weekday[date('w' , $time)]; //一周内（夸周）
					break;
				
				case $t < 60 * 60 * 24 * 30:
					$text = floor($t / ( 60 * 60 * 24 )) . '天前'; //一个月内
					break;
				case $t < 60 * 60 * 24 * 365 && $y == 0:
					$text = date('m-d' , $time); //一年内
					break;
				default:
					$text = date('Y-m-d' , $time); //一年以前
					break;
			}
			
			return $text;
		}
		
		
		/**
		 * @todo    将日期转成---的形式 如2016年04月26日 转成2016-04-26
		 * @author  zhouquan
		 * @date    : 2016年4月26日
		 */
		public static function dateSwitch ( $date ) {
			$arr = explode('年' , $date);
			
			$year = $arr[0];
			
			$arr = explode('月' , $arr[1]);
			
			$month = $arr[0];
			
			$arr = explode('日' , $arr[1]);
			
			$day = $arr[0];
			
			return $year . '-' . $month . '-' . $day;
		}
		
		
		/**
		 * 根据经纬度计算距离（返回单位:米）
		 * @author Malcolm
		 * @param  其中A ($lat1,$lng1)、B($lat2,$lng2)
		 * @return array
		 * @date   16/5/23
		 */
		public static function getDistance ( $lat1 , $lng1 , $lat2 , $lng2 ) {
			//地球半径
			$R = 6378137;
			if ( $lat1 && $lng1 && $lat2 && $lng2 ) {
				//将角度转为狐度
				$radLat1 = deg2rad($lat1);
				$radLat2 = deg2rad($lat2);
				$radLng1 = deg2rad($lng1);
				$radLng2 = deg2rad($lng2);
				
				//结果
				$s = acos(cos($radLat1) * cos($radLat2) * cos($radLng1 - $radLng2) + sin($radLat1) * sin($radLat2)) * $R;
				
				//精度
				$s = round($s * 10000) / 10000;
				
				return round($s);
			} else {
				return 0.1;
			}
			
		}
		
		/**
		 * 友好显示距离
		 * @author 刘小祥 (2016年5月23日)
		 * @param int $distance 距离
		 * @return string
		 */
		public static function humanDistance ( $distance ) {
			if ( $distance > 0 && $distance < 1000 ) {
				$distance = $distance . "m";
			}
			if ( $distance > 1000 ) {
				$distance = round($distance / 1000) . "km";
			}
			if ( !$distance ) {
				$distance = "0m";
			}
			
			return $distance;
		}
		
		
		/**
		 * @todo    友好的显示价格
		 * @author Malcolm  (2018年04月13日)
		 */
		public static function humanPrice($num,$unit = true ){
				if( $num > 1000 ){
					$num = $num / 1000;
					
					if($unit)
						$num .= 'k';
				}
				
				return $num;
		}
		
		public static function debug ( $str , $append = "" ) {
			ob_start();
			echo date("[Y-m-d H:i:s] ");
			echo "################################\n";
			print_r($str);
			echo "\n";
			$content = ob_get_contents();
			$name    = APP . "_" . ACT;
			file_put_contents(APP_PATH . "/log/{$name}{$append}.log" , $content , FILE_APPEND);
			ob_end_clean();
		}
		
		
		/**
		 * @todo    通用图片上传方法
		 * @author  Malcolm  (2018年01月31日)
		 */
		public static function upload ( $name = '' , $isFull = 2 , $fileName = '' ) {
			import('alioss.class');
			if ( $name )
				$res = Oss::upload($name , $fileName);
			else {
				$dateDir = date("/Y/m/d/H");
				$tmp     = "attachment{$dateDir}";
				$res     = Oss::upload($tmp , $fileName);
			}
			
			
			if ( 1 == $isFull )
				return $res;
			
			
			if ( $res['status'] == 1 )
				return $res['data'][0];
			
			return false;
		}


        /**
         * @todo    通用图片批量上传
         * @author Malcolm  (2018年06月20日)
         */
        public static function uploads($name){
            import('alioss.class');

            //先上传到临时目录
            $tmpArr = Hera::uploadArr($name);

            log::jsonInfo('####本地图片路径####');
            log::jsonInfo($tmpArr);
            log::jsonInfo('####本地图片路径####');

            $list = [];

            //再传到OSS
            if (is_array($tmpArr)) {
                foreach ($tmpArr as $key => $val) {
                    $list[] = Hera::uploadFileByLocal($val);
                }
            }

            log::jsonInfo('####OSS图片路径####');
            log::jsonInfo($list);
            log::jsonInfo('####本地图片路径####');

            return $list;
        }


        /**
         * @todo    上传本地图片至OSS服务器
         * @author Malcolm  (2018年06月20日)
         */
        public static function uploadFileByLocal($path){
            if(!file_get_contents($path))
                return '获取不到';

            import('alioss.class');

            $res = Oss::uploadFileByLocal($path);

            return $res['data'][0];
        }



        /**
		 * @todo    通用文件上传方法
		 * @author  Malcolm  (2018年01月31日)
		 */
		public static function uploadFile ( $name = '' , $isFull = 2 , $fileName = '' ) {
			import('alioss.class');
			if ( $name )
				$res = Oss::uploadFile($name , $fileName);
			else {
				$dateDir = date("/Y/m/d/H");
				$tmp     = "attachment{$dateDir}";
				$res     = Oss::uploadFile($tmp , $fileName);
			}

			if ( 1 == $isFull )
				return $res;


			if ( $res['status'] == 1 )
				return $res['data'][0];

			return false;
		}


        /**
         * @todo    上传外链文件
         * @author Malcolm  (2018年05月25日)
         */
		public static function uploadFileByUrl($url,$isFull = 2){
            //是否是本站图片
            if (strpos($url, IMG_URL) !== false) {
                return '123121';
            }


            return false;
        }




		
		/**
		 * @todo    验证短信验证码
		 * @author  Malcolm  (2018年01月31日)
		 */
		public static function checkSMSCode ( $mobile , $code ) {
			if ( !$mobile || !$code )
				return false;
			
			$rs = Common::check_sign($mobile , $code);
			
			return $rs['status'];
		}
		
		
		/**
		 * @todo    二维数组排序
		 * @author  Malcolm  (2018年04月13日)
		 * @param  array  $arrays     需要排序的数组
		 * @param  string $sort_key   排序的键值名
		 * @param int     $sort_order 排序顺序   SORT_ASC升序   SORT_DESC 降序
		 * @param int     $sort_type  排序方法  SORT_REGULAR常规排序  SORT_NUMERIC数字排序 SORT_STRING排序
		 * @return array
		 */
		public static function arraySort ( $arrays , $sort_key , $sort_order = SORT_ASC , $sort_type = SORT_NUMERIC ) {
			if ( is_array($arrays) ) {
				foreach ( $arrays as $array ) {
					if ( is_array($array) ) {
						$key_arrays[] = $array[$sort_key];
					} else {
						return false;
					}
				}
			} else {
				return false;
			}
			array_multisort($key_arrays , $sort_order , $sort_type , $arrays);
			
			return $arrays;
		}


        /**
         * @todo    通用上传（多图）
         * @author Malcolm  (2018年06月20日)
         */
        static public function uploadArr($name, $dir="", $width=0, $height=0, &$tooSmall=0){
            $allowedExts = array("jpg", "jpeg", "gif", "png");
            $fileData = $_FILES[$name];
            $fileList = $fileData['tmp_name'];
            if (!$fileList) {
                return array();
            }
            if (!is_array($fileList)) {
                $fileList = array($fileList);
                $tempData = $fileData;
                $fileData = array();
                $fileData['error'][0] = $tempData['error'];
                $fileData['name'][0] = $tempData['name'];
            }
            $images = array();
            foreach ($fileList  as $key=>$row) {
                if ($fileData['error'][$key]!==0) {
                    continue;
                }
                $tempFile = $row;
                $filename = $fileData['name'][$key];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                if (!in_array($ext, $allowedExts)) {
                    continue;
                }
                $imgPath = Zeus::createImagePath($dir, $ext);
                $rs = @move_uploaded_file($tempFile, '/tmp'.$imgPath);
                if ($rs) {
                    $images[] = '/tmp'.$imgPath;
                } else {
                    $realPath = '/tmp'.$imgPath;
                    if ($width || $height) {
                        $imageInfo = getimagesize($realPath);
                        $imageWidth = $imageInfo['width'];
                        $heightWidth = $imageInfo['height'];
                    }
                    if ($width && $imageWidth<$width) {
                        $tooSmall=1;
                    }
                    if ($height&& $heightWidth<$height) {
                        $tooSmall=1;
                    }
                }
            }
            return $images;
        }
	}
