<?php
	
	/**
	 * 资讯模型
	 * Created by Malcolm.
	 * Date: 2018/3/6  15:24
	 */
	class NewsMod extends CBaseMod {
		public function __construct() {
			parent::__construct('news');
			
		}
		
		public function getInfo ( $id ) {
			$info =  parent::getInfo($id);
			
			//标签
			$tagArr = explode(',',$info['tag_ids']);
			
			//日期
			$info['add_date'] = date('Y/m/d',$info['add_time']);
			
			if ( is_array($tagArr) ) {
				foreach ( $tagArr as $key => $val ) {
					$info['tag_name'][] = [
						'tag_id' =>$val,
						'tag_name' =>Zeus::config('news_tag',$val),
					];
				}
			}
			
			return $info;
		}
		
		/**
		 * @todo    获取列表信息
		 * @author Malcolm  (2018年04月10日)
		 */
		public function getListInfo($id){
			$info = $this->getInfo($id);
			
			$data = [
				'news_id' =>$info['id'],
				'news_title' =>$info['title'],
				'news_short_info' =>$info['short_info'],
				'news_cover' =>$info['cover'],
				'news_tag_list' =>$info['tag_name'],
				'news_add_date' =>Hera::humanDate($info['add_time']),
			];
			
			return $data;
		}
		
		
		/**
		 * @todo    获取首页列表
		 * @author Malcolm  (2018年04月13日)
		 */
		public function getHomeList(){
			$query['cond'] = " `cover` IS NOT NULL AND mark = 1 ";
			$query['limit'] = 5;
			$query['fields'] = 'id';
			
			$data = $this->getData($query);
			
			$list = [];
			if ( is_array($data) ) {
				foreach ( $data as $key => $val ) {
					$info = $this->getInfo($val['id']);
					$list[] = [
						'news_id' =>$info['id'],
						'news_title' =>$info['title'],
						'news_cover' =>$info['cover'],
					];
			    }
			}
			
			return $list;
		}
		
		
		public function getDetailInfo ( $id ) {
			$info = $this->getInfo($id);
			
			$data = [
				'news_id' =>$info['id'],
				'news_title' =>$info['title'],
				'news_short_info' =>$info['short_info'],
				'news_content' =>$info['content'],
				'news_add_date' =>date('Y/m/d H:i:s',$info['add_time']),
				'news_share_url' => WAP_URL."/?app=share&act=news&id={$info['id']}"
			];
			
			return $data;
		}
		
	}