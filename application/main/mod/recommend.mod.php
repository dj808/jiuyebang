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
				],
                [
                    'id'   => 4 ,
                    'name' => '每日分享'
                ]
			];
			
		}
		
		
		/**
		 * @todo    获取列表信息
		 * @author  Malcolm  (2018年04月12日)
		 */
		public function getListInfo ( $id ) {
			$info = parent::getInfo($id);
			
			$info['type_name'] = $info['type'] == 1 ? '跳转APP内容' : 'H5';
			
			if ( is_array($this->plateList) ) {
				foreach ( $this->plateList as $k => $v ) {
					if ( $info['plate'] == $v['id'] )
						$info['plate_name'] = $v['name'];
				}
			}
			
			if($info['type']==2)
				$info['plate_name'] = ' ';
			$info['logo']=$info['cover'];
			return $info;
		}
		
		
	}