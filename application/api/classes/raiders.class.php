<?php
	
	
	/**
	 * 攻略、秘籍、试卷 控制器拓展
	 * Created by Malcolm.
	 * Date: 2018/4/17  13:42
	 */
	class Raiders extends Zeus {
		public $mod;
		public  function __construct() {
			parent::__construct("raiders");
			
			$this->mod = m('raiders');
			false&&$this->mod = new RaidersMod();
		}
		
		/**
		 * @todo    获取列表
		 * @author Malcolm  (2018年04月17日)
		 */
		public function getList($param,$userId,$isIn = false){
			//排序条件
			$order = intval($param['order_id'])?intval($param['order_id']):1;
			
			if($order){
				switch ($order){
					case 1:
						$orderBy = '  id DESC ';
						break;
					
					case 2:
						$orderBy = '  look_num DESC  ';
						break;
					
					case 3:
						$orderBy = '  money_upper DESC ';
						break;
					
					case 4:
						$orderBy = '  money_lower ASC';
						break;
					
					default:
						$orderBy = '  id DESC ';
				}
			}
			
			
			$type = intval($param['type']);
			if(!$type)
				return message('参数丢失');
			
			$cond[] = " type = {$type} ";
			
			//分类条件
			$cateId = intval($param['cate_id']);
			if($cateId)
				$cond[] =  " cate_id = {$cateId} ";
			
			//价格条件
			$money = intval($param['money_id']);
			if($money){
				switch ($money){
					case 1:
						$cond[] = " price <= 50 ";
						break;
						
					case 2:
						$cond[] = " price <= 100  AND price >=50 ";
						break;
						
					case 3:
						$cond[] = " price <= 500 AND price >=100 ";
						break;
						
					case 4:
						$cond[] = " price >= 500 ";
						break;
						
					default :
						$cond[] = " price <= 10 ";
						
				}
			}
			
			$isHot =  intval($param['is_hot']);
			if($isHot==1)
				$cond[] = " is_hot = 1 ";
                        
                        $isChoice=  intval($param['is_choice']);
			if($isChoice==1)
				$cond[] = " is_choice = 1 ";
				
                        $isFree = intval($param['is_free']);
                        if($isFree==1)
                                $cond[] = " price = 0.00 ";
                                
			$cond[] = "mark = 1";
			
			
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
			//bi'a
                        
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
		 * @todo    获取 攻略/秘籍/试卷 筛选选项
		 * @author Malcolm  (2018年04月17日)
		 */
		public function getRaidersOptionList($param){
			$type = intval($param['type']);
			
			if(!$type)
				return message('参数丢失');
			
			//价格
			$money = [
				[
					'money_id' =>0,
					'money_name' =>'不限',
				],
				
				[
					'money_id' =>1,
					'money_name' =>'50以下',
				],
				
				[
					'money_id' =>2,
					'money_name' =>'50 - 100',
				],
				
				[
					'money_id' =>3,
					'money_name' =>'100 - 500',
				],
				
				[
					'money_id' =>4,
					'money_name' =>'500以上',
				]
			];
			
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
					'order_name' =>'薪资最高',
				],
				
				[
					'order_id' =>4,
					'order_name' =>'薪资最低',
				],
			
			];
			
			//分类
			$query = [
				'fields' => 'id',
				'cond' => " type = {$type} AND parent_id != 0 AND mark = 1 ",
				'order_by' =>'sort ASC , id DESC'
			];
			
			$cateMod = m('cate');
			
			$tmp = $cateMod->getData($query);
			
			$cate = [];
			if ( is_array($tmp) ) {
				foreach ( $tmp as $key => $val ) {
					$info = $cateMod->getInfo($val['id']);
					
					$cate[] = [
						'cate_id' => $info['id'],
						'cate_name' => $info['name'],
					];
				}
			}
			
			$data = [
				'order_list' =>$order,
				'cate_list' =>$cate,
				'money_list' =>$money,
			];
			
			return message('操作成功', true ,$data);
		}
		
		
		/**
		 * @todo    获取 攻略/秘籍/试卷 详情
		 * @author Malcolm  (2018年04月17日)
		 */
		public function getRaidersInfo($param,$userId){
			$id = intval($param['raiders_id']);
			
			if(!$id)
				return message('参数丢失');
			
			$info = $this->mod->getDetailInfo($id);
			
			//自动维护查看次数
			$this->mod->editColumnValue($id,'look_num');
			
			$tmp = $this->mod->getInfo($id);
			
			switch ($tmp['type']){     //1：秘籍；2：试卷；3攻略
				case 1:
					$collectType = 6;
					$commentType = 2;
					break;
					
				case 2:
					$collectType = 5;
					$commentType = 4;
					break;
					
				case 3:
					$collectType = 4;
					$commentType = 3;
					break;
					
				default:
					$collectType = 6;
					$commentType = 2;
					
			}
			
			
			//是否已收藏
			if($userId){
				$cond = " type = {$collectType} AND type_id = {$id} AND user_id = {$userId} AND mark = 1 ";
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
			$param['type'] = $commentType;
			$param['type_id'] = $id;
			$param['parent_id'] = 0;
			
			$info['comment_list'] = ic('comment')->getList($param,$userId,true);
			
			return message('操作成功', true ,$info);
		}
		
		
		
	}