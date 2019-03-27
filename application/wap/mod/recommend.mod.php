<?php
	
	/**
	 * Created by PhpStorm.
	 * User: dj
	 * Date: 2018/3/19
	 * Time: 14:52
	 */
	class RecommendMod extends CBaseMod {
		public  $plateList;
		
		/**
		 * 构造函数
		 */
		public function __construct () {
			parent::__construct('recommend');
			
			$this->plateList = [
				[
					'id'   => 1 ,
					'name' => '兼职'
				] ,
				
				[
					'id'   => 2 ,
					'name' => '全职'
				] ,
				
				[
					'id'   => 3 ,
					'name' => '培训'
				]
			];
			
		}
		
		
		/**
		 * @todo    获取列表信息
		 * @author  Malcolm  (2018年04月12日)
		 */
		public function getListInfo ( $id ) {
			$info = parent::getInfo($id);
			
			$data = [
				'recommend_id' =>$info['id'],
				'recommend_title' =>$info['title'],
				'recommend_type' =>$info['type'],
				'recommend_plate' =>$info['plate'],
				'recommend_type_id' =>$info['type_id'],
				'recommend_cover' =>$info['cover'],
				'recommend_url' =>$info['content'],
				'recommend_add_date' =>date('Y/m/d',$info['add_time']),
			];
			
			
			return $data;
		}
		
		
	}