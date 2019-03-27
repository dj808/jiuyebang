<?php
	
	/** 培训控制器 拓展
	 * Created by Malcolm.
	 * Date: 2018/4/16  09:49
	 */
	class Train extends Zeus {
		public $mod;
		public  function __construct() {
			parent::__construct("training");
			
			$this->mod = m('training');
			false&&$this->mod = new TrainingMod();
		}
		
		
		
		/**
		 * @todo    获取培训页面搜索条件
		 * @author Malcolm  (2018年04月16日)
		 */
		public function getConditionForTrain($param){
			//行业
			$industry = Zeus::config('train_industry_list');
			
			$industry[0] = '不限';
			$industryList = [];
			if ( is_array($industry) ) {
				foreach ( $industry as $key => $val ) {
					$industryList[] = [
						'industry_id' => $key,
						'industry_name' => $val,
					];
				}
			}
			$industryList = Hera::arraySort($industryList,'industry_id');
			
			
			//级别
			$level = Zeus::config('train_level_list');
			$level[0] = '不限';
			$levelList = [];
			
			if ( is_array($level) ) {
				foreach ( $level as $key => $val ) {
					$levelList[] = [
						'level_id' => $key,
						'level_name' => $val,
					];
				}
			}
			$levelList = Hera::arraySort($levelList,'level_id');
			
			
			//周期
			$cycle = Zeus::config('train_cycle_list');
			$cycle[0] = '不限';
			$cycleList = [];
			
			if ( is_array($cycle) ) {
				foreach ( $cycle as $key => $val ) {
					$cycleList[] = [
						'cycle_id' => $key,
						'cycle_name' => $val,
					];
				}
			}
			$cycleList = Hera::arraySort($cycleList,'cycle_id');
			
			
			//排序
			$order = [
				[
					'order_id' =>1,
					'order_name' =>'综合排序',
				],
				
				[
					'order_id' =>2,
					'order_name' =>'人气最高',
				],
				
				[
					'order_id' =>3,
					'order_name' =>'价格最高',
				],
				
				[
					'order_id' =>4,
					'order_name' =>'价格最低',
				],
			
			];
			
			$data = [
				'industry_list' =>$industryList,
				'level_list' =>$levelList,
				'cycle_list' =>$cycleList,
				'order_list' =>$order,
			];
			
			return message('操作成功',true,$data);
			
		}
		
		
		/**
		 * @todo    获取培训列表
		 * @author Malcolm  (2018年04月16日)
		 */
		public function getList($param,$userId,$isIn=false){
			
			//排除本身
			$not = intval($param['not']);
			if($not)
				$cond[] = " id <> {$not} ";
			
			//关键字
			$keyword = $param['keyword'];
			if($keyword){
				if(is_array($keyword)){
					if ( is_array($keyword) ) {
						$tmp = [];
						foreach ( $keyword as $key => $val ) {
							$tmp[] = " `title` LIKE '%{$val}%'  ";
						}
						
						$tmp = implode(' OR ',$tmp);
						
						$cond[] = "( {$tmp} )";
					}else{
						$cond[] = " `title` LIKE '%{$keyword}%'  ";
					}
				}
				
			}
			
			
			//行业条件
			$industryId = intval($param['industry_id']);
			if($industryId)
				$cond[] = " FIND_IN_SET('{$industryId}',`industry_id`) ";
			
			//级别条件
			$levelId = intval($param['level_id']);
			if($levelId)
				$cond[] = " FIND_IN_SET('{$levelId}',`level_id`) ";
			
			//周期条件
			$cycleId = intval($param['cycle_id']);
			if($cycleId)
				$cond[] = " FIND_IN_SET('{$cycleId}',`cycle_id`) ";
			
			
			$cond[] = " status = 1 AND mark = 1 ";
			
			//排序条件
			$order = intval($param['order_id'])?intval($param['order_id']):1;
			if($order){
				switch ($order){
					case 1:
						$orderBy[] = '  id DESC ';
						break;
					
					case 2:
						$orderBy[] = '  look_num DESC  ';
						break;
					
					case 3:
						$orderBy[] = '  price DESC ';
						break;
					
					case 4:
						$orderBy[] = '  price ASC';
						break;
					
					default:
						$orderBy[] = '  id DESC ';
				}
			}
			
			if(is_array($orderBy))
				$orderBy = implode(',',$orderBy);
			
			
			$page = $perpage = $limit = 0;
			$this->initPage($page, $perpage, $limit);
			
			$query = [
				'fields' => 'id',
				'order_by' => $orderBy,
				'cond' => $cond,
				'limit' => $limit,
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
		 * @todo    获取培训详情
		 * @author Malcolm  (2018年04月16日)
		 */
		public function getTrainInfo($param,$userId){
			$id = intval($param['train_id']);
			
			if(!$id)
				return message('参数丢失');
			
			//自动维护职位查看次数
			$this->mod->editColumnValue($id,'look_num');
			
			$info = $this->mod->getDetailInfo($id);
			
			//是否已经收藏
			if($userId){
				$cond = " type = 8 AND type_id = {$id} AND user_id = {$userId} AND mark = 1 ";
				$count = m('collect')->getCount($cond);
				
				$info['is_collect'] = $count?1:2;
				
				$info['collect_id'] = 0;
				
				if($info['is_collect']==1){
					$collectId = m('collect')->getIds($cond);
					$info['collect_id'] = $collectId[0];
				}
				
			}else{
				$info['is_collect'] = 2;
				$info['collect_id'] = 0;
			}
			
			return message('操作成功', true ,$info);
		}
		
	}