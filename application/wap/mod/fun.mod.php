<?php
	
	/**
	 * 趣事模型
	 * Created by Malcolm.
	 * Date: 2018/3/6  15:24
	 */
	class FunMod extends CBaseMod {
		public function __construct() {
			parent::__construct('fun');
			
		}
		
		
		/**
		 * @todo    获取我的发布列表信息
		 * @author Malcolm  (2018年03月06日)
		 */
		public function getMyListInfo($id){
			$info = $this->getInfo($id);
			
			//地址
			$cityName = $this->cityMod->getCityNameByDepth($info['area_id'],2);
			
			$data = [
				'fun_id' =>$info['id'],
				'title' =>$info['title'],
				'look_num' =>$info['look_num'],
				'city_name' =>$cityName,
				'add_date' =>date('Y-m-d',$info['add_time']),
			];
			
			return $data;
		}
		
		
		/**
		 * @todo    获取列表信息
		 * @author Malcolm  (2018年04月10日)
		 */
		public function getListInfo($id){
			$info = $this->getInfo($id);
			
			$content = Zeus::getStrByHtml($info['content']);
			
			$data = [
				'fun_id' =>$info['id'],
				'fun_title' =>$info['title'],
				'fun_short_info' =>mb_substr($content,0,30,'utf-8'),
				'fun_images' =>$info['images'],
				'fun_add_date' =>date('Y-m-d',$info['add_time']),
			];
			
			return $data;
		}
		
		
	}