<?php
	
	/**
	 * 学校模型
	 * Created by Malcolm.
	 * Date: 2018/2/5  10:03
	 */
	class CollegeMod extends CBaseMod {
		public function __construct() {
			parent::__construct('college');
		}
		
		
		/**
		 * @todo    获取列表信息
		 * @author Malcolm  (2018年02月05日)
		 */
		public function getListInfo($id) {
			$info = $this->getInfo($id);
			
			$data = [
				'college_id'=>$id,
				'college_name'=>$info['name'],
			];

			
			return $data;
		}
		
		
	}