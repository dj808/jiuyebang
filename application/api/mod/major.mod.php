<?php
	
	/**
	 * 专业模型
	 * Created by Malcolm.
	 * Date: 2018/2/5  11:08
	 */
	class MajorMod extends CBaseMod {
		public function __construct() {
			parent::__construct('major');
		}
		
		
		/**
		 * @todo    获取列表信息
		 * @author Malcolm  (2018年02月05日)
		 */
		public function getListInfo($id) {
			$info = $this->getInfo($id);
			
			$data = [
				'major_id'=>$id,
				'major_name'=>$info['name'],
			];

			if($info['pid'] ==0)
				$data['level'] = 1;
			else
				$data['level'] = 2;
			
			return $data;
		}
		
	}