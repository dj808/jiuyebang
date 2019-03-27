<?php
	/**
	 * 发现控制器拓展
	 * Created by Malcolm.
	 * Date: 2018/4/17  10:56
	 */
	class News extends Zeus {
		public $mod;
		public  function __construct() {
			parent::__construct("user");
			
			$this->mod = m('news');
			false&&$this->mod = new NewsMod();
		}

		/**
		 * @todo    获取每日分享列表
		 * @author Malcolm  (2018年04月17日)
		 */
		public function getNewsList($param,$isIn = false){
			$cateId = intval($param['cate_id']);
			
			if($cateId)
				$cond[] = " cate_id = {$cateId} ";
			
			$cond[] = " mark = 1 ";
			
			$page = $perpage = $limit = 0;
			$this->initPage($page, $perpage, $limit);
			
			$query = [
				'fields' => 'id',
				'cond' => $cond,
				'limit' => $limit,
				'order_by' => ' id DESC ',
			];
			
			$list = $this->mod->getData($query);
			$count = $this->mod->getCount($query['cond']);
			
			$newList = [];
			
			if ( is_array($list) ) {
				foreach ( $list as $key => $val ) {
					$newList[] = $this->mod->getListInfo($val['id']);
				}
			}
			
			$data = [
				'count'=>$count,
				'page'=>$page,
				'perpage'=>$perpage,
				'list'=>$newList
			];
			
			if($isIn)
				return $data;
			
			
			return message('操作成功', true ,$data);
			
		}
		
		
		/**
		 * @todo    获取每日分享详情
		 * @author Malcolm  (2018年04月17日)
		 */
		public function getNewsInfo($param,$userId){
			$id = intval($param['news_id']);
			
			$info = $this->mod->getDetailInfo($id);
			
			//自动维护查看次数
			$this->mod->editColumnValue($id,'look_num');
			
			//是否已收藏
			if($userId){
				$cond = " type = 7 AND type_id = {$id} AND user_id = {$userId} AND mark = 1 ";
				$count = m('collect')->getCount($cond);
				
				$info['is_collect'] = $count?1:2;
				
				$info['collect_id'] = 0;
				
				if($info['is_collect']==1){
					$collectId = m('collect')->getIds($cond);
					$info['collect_id'] = $collectId[0];
				}
			}else{
				$info['collect_id'] = 0;
				$info['is_collect'] = 2;
			}
			
			//查询评论
			//留言
			$param['type'] = 6;
			$param['type_id'] = $id;
			$param['parent_id'] = 0;
			
			$info['comment_list'] = ic('comment')->getList($param,$userId,true);
			
			return message('操作成功', true ,$info);
		}
		
		
	}