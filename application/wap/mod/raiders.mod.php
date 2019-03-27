<?php
	
	/**
	 * 攻略、试卷、秘籍模型
	 * Created by Malcolm.
	 * Date: 2018/3/6  15:24
	 */
	class RaidersMod extends CBaseMod {
		public function __construct() {
			parent::__construct('raiders');
			
		}
		
		
		/**
		 * @todo    获取列表信息
		 * @author Malcolm  (2018年04月10日)
		 */
		public function getListInfo($id){
			$info = $this->getInfo($id);
			
			$content = Zeus::getStrByHtml($info['content']);
			
			$data = [
				'raiders_id' =>$info['id'],
				'raiders_title' =>$info['title'],
				'raiders_short_info' =>mb_substr($content,0,30,'utf-8'),
				'raiders_cover' =>$info['cover_img'],
				'raiders_price' =>$info['price'],
				'raiders_add_date' =>date('Y/m/d',$info['add_time']),
			];
			
			if($data['raiders_price']==0)
				$data['raiders_price'] = '免费';
			
			return $data;
		}
		
		
		/**
		 * @todo    详情
		 * @author Malcolm  (2018年04月17日)
		 */
		public function getDetailInfo($id){
			$info = $this->getInfo($id);
			
			$data = [
				'raiders_id' =>$info['id'],
				'raiders_title' =>$info['title'],
				'raiders_cover' =>$info['cover_img'],
				'raiders_price' =>$info['price'],
				'raiders_content' =>$info['content'],
				'raiders_add_date' =>date('Y/m/d H:i:s',$info['add_time']),
				'raiders_share_url' => WAP_URL."/?app=share&act=raiders&id={$info['id']}"
			];
			
			if($data['raiders_price']==0)
				$data['raiders_price'] = '免费';
			
			return $data;
		}
		
	}