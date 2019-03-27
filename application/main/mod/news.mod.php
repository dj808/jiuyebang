<?php
	/**
	 * Created by PhpStorm.
	 * User: dj
	 * Date: 2018/4/24
	 * Time: 15:52
	 */
	
	/**
	 * @todo   资讯管理模型
	 * @author dingj (2018年4月23日)
	 */
	class NewsMod extends CBaseMod {
		/**
		 * 构造函数
		 */
		public function __construct () {
			parent::__construct('news');
			
		}
		
		/**
		 * @todo    获取资讯列表
		 * @author  dingj  (2018年04月24日)
		 */
		public function getNewsInfo ( $id ) {
			$info = parent::getInfo($id);
			
			$info['add_time']  = $info['add_time'] ? date('Y-m-d H:i:s' , $info['add_time']) : '';//添加时间
			//获取分类信息
			$cate              = $this->cateMod->getInfo($info['cate_id']);
			$info['cate_name'] = $cate['name'];
			//标签
			$news_tag          = Zeus::config('news_tag');
			$info['tag_name']  = $news_tag[$info['tag_ids']];
			//添加人
			$adminInfo         = $this->adminMod->getInfo($info['add_user']);
			$info['add_user']  = $adminInfo['unick'];
			$info['logo']=$info['cover'];
			return $info;
		}
		
		
		/**
		 * @todo    获取分类列表
		 * @author Malcolm  (2018年05月15日)
		 */
		public function getCateList(){
			$cond[] = "parent_id=4 AND mark = 1";
			
			$cateMod= $this->cateMod;
			$ids = $cateMod->getIds($cond);
			$data = [];
			if ( is_array($ids) ) {
				foreach ( $ids as $key => $val ) {
					$info = $cateMod->getInfo($val);
					$data[] = [
						'id' => $info['id'],
						'name' => $info['name'],
					];
			    }
			}
			
			return $data;
		}
		
	}