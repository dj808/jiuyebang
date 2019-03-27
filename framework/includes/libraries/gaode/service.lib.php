<?php
	
	/**
	 * 高德服务
	 * Created by Malcolm.
	 * Date: 2018/1/5  18:11
	 */
	class Service {
		protected $key;
		
		public function __construct()   {
			$this->key = 'def26bbdc66236a6709b45e3f13b9d77';
			
		}
		
		
		/**
		 * @todo    逆地理编码
		 * @author Malcolm  (2018年01月05日)
		 */
		public function getAddressByGps($gps){
			$url = "http://restapi.amap.com/v3/geocode/regeo?output=JSON&key={$this->key}";
			
			//拼接gps信息
			$url .="&location={$gps}";
			
			$rs = $this->getCurlByUrl($url);
			
			return $rs;
		}
		
		/**
		 * @todo    地理编码
		 * @author Malcolm  (2018年01月05日)
		 */
		
		public function getGpsByAddress ( $address ) {
			$url = "http://restapi.amap.com/v3/geocode/geo?output=JSON&key={$this->key}";
			
			//拼接地址
			$url .="&address={$address}";
			$rs = $this->getCurlByUrl($url);
			
			return $rs;
		}
		
		
		/**
		 * @todo    GPS坐标转高德坐标
		 * @author Malcolm  (2018年03月24日)
		 */
		public function getGaoDeGps($gps){
			$url = "http://restapi.amap.com/v3/assistant/coordinate/convert?&key={$this->key}&coordsys=gps";
			
			//拼接gps信息
			$url .="&locations={$gps}";
			
			$rs = $this->getCurlByUrl($url);
			
			return $rs;
		}
		
		
		
		protected function getCurlByUrl($url){
			//初始化
			$curl = curl_init();
			//设置url
			curl_setopt($curl, CURLOPT_URL, $url);
			
			//设置头文件的信息作为数据流输出
			curl_setopt($curl, CURLOPT_HEADER, 0);
			//设置获取的信息以文件流的形式返回，而不是直接输出。
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($curl);
			
			//关闭URL请求
			curl_close($curl);
			
			return json_decode($output,true);
		}
		
		
		
	}