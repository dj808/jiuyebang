<?php
	/**
	 * 高德云图基类
	 * Created by wangqs
	 * Date: 2017/9/18 17:11
	 */
	
	
	class YunTuBasic {
		protected $key;
		protected $sig;
		protected $table_id;
		protected $url;
		public function __construct()   {
			$this->key = GD_YUN_KEY;
			
			$this->url = 'http://yuntuapi.amap.com/datamanage/data/';
			
			if(MILIEU=='RD')
				$this->table_id = GD_TABLE_ID_RD;
			else
				$this->table_id = GD_TABLE_ID_SERVER;
		}
		
		
		/**
		 * @todo    新增/修改数据
		 * @author wangqs  (2017年09月18日)
		 * @param $data array 更新数据
		 * @param $id int 云图数据库ID
		 * @return array|bool
		 */
		public function edit($data,$tableId = ''){
			//先查询有没有该手机号的数据
			
			$_id = $this->getIdByMobile($data['mobile']);
			
			if($_id) {
				$operate = 'update';
				$data['_id'] = $_id;
			} else {
				$operate = 'create';
			}
			
			if(!$tableId)
				$tableId = $this->table_id;
			
			$rs = self::curl($operate,$data,$tableId);
			
			if(!$rs || !$rs['status'])    return false;
			
			return true;
		}
		
		
		/**
		 * @todo    仅编辑
		 * @author wangqs  (2017年12月19日)
		 */
		public function justEdit($data,$yuntuId){
			$data['_id'] = $yuntuId;
			$rs = self::curl('update',$data);
			
			if(!$rs || !$rs['status'])    return false;
			
			return true;
		}
		
		
		/**
		 * @todo    删除数据
		 * @author wangqs  (2017年09月18日)
		 */
		public function drop($id){
			$operate = 'delete';
			
			$rs = self::curl($operate,$id);
			
			if(!$rs || !$rs['status'])    return false;
			
			return true;
		}
		
		
		/**
		 * @todo    根据用户手机号，获取云图ID
		 * @author Malcolm  (2018年01月31日)
		 */
		public function getIdByMobile($mobile){
			$url = 'http://yuntuapi.amap.com/datasearch/local?';
			
			$key = $this->key;
			$tableid = $this->table_id;
			
			$url .= "tableid={$tableid}&key={$key}&keywords={$mobile}&city=全国&filter=mobile:{$mobile}&limit=1";
			
			$data = $this->getCurlByUrl($url);
			
			$id = $data['datas'][0]['_id'];
			
			//return $data;
			return $id?$id:false;
		}
		
		
		/**
		 * @todo    获取一条数据
		 * @author wangqs  (2017年12月19日)
		 */
		public function getOne($yunId){
			$url = 'http://yuntuapi.amap.com/datasearch/id?';
			
			$key = $this->key;
			$tableid = $this->table_id;
			
			$url .= "tableid={$tableid}&key={$key}&_id={$yunId}";
			
			$data = $this->getCurlByUrl($url);
			
			return $data;
		}
		
		/**
		 * @todo    获取数据
		 * @author wangqs  (2017年09月25日)
		 */
		public function getList($lng,$lat,$wd,$page=1,$perpage=10,$userId=0){
			$radius = 5000;  //搜索半径 单位米
			$limit = 100;
			if($userId)
				$data = "&center={$lng},{$lat}&radius={$radius}&sortrule=_distance:1&limit={$perpage}&page={$page}";
			else
				$data = "&center={$lng},{$lat}&radius={$radius}&sortrule=_distance:1&limit={$perpage}&page={$page}";
			
			if($wd)
				$data .= '&keywords= '.$wd;
			else
				$data .= '&keywords=   ';
			
			$rsData = self::getCurl($data);
			
			return $rsData;
		}
		
		
		/**
		 * @todo    连接高德API服务
		 * @author wangqs  (2017年09月18日)
		 * @param $operate  string 地址后缀
		 * @param $postData array 操作的数据
		 * @return array
		 */
		protected function curl($operate,$data,$tableId){
			$url = $this->url.$operate;
			
			
			
			$postData['key'] = $this->key;
			$postData['tableid'] = $tableId;
			
			$postData['loctype'] = 1;
			
			//如果是删除
			if($operate == 'delete') {
				$postData['ids'] = $data;
			}else{
				//data转json
				$postData['data'] = json_encode($data);
			}
			
			//初始化
			$curl = curl_init();
			//设置url
			curl_setopt($curl, CURLOPT_URL, $url);
			//设置头文件的信息作为数据流输出
			curl_setopt($curl, CURLOPT_HEADER, 0);
			//设置获取的信息以文件流的形式返回，而不是直接输出。
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			//设置post方式提交
			curl_setopt($curl, CURLOPT_POST, 1);
			//设置post数据
			curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
			//执行命令
			$data = curl_exec($curl);
			
			
			//关闭URL请求
			curl_close($curl);
			
			return json_decode($data,true);
		}
		
		
		/**
		 * @todo    获取数据连接
		 * @author wangqs  (2017年09月25日)
		 */
		protected function getCurl($data){
			$url = 'http://yuntuapi.amap.com/datasearch/around?key='.$this->key.'&tableid='.$this->table_id.'&city=全国';
			
			//拼接url
			$url .= $data;
			
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