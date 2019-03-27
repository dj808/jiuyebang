<?php
	
	
	/**
	 * 发现控制器拓展
	 * Created by Malcolm.
	 * Date: 2018/4/17  10:56
	 */
	class Find extends Zeus {
		public $mod;
		public  function __construct() {
			parent::__construct("user");
			
			$this->mod = m('user');
			false&&$this->mod = new UserMod();
		}
		
		
		
		/**
		 * @todo    获取每日分享分类列表
		 * @author Malcolm  (2018年04月17日)
		 */
		public function getNewsCateList(){
			$cateMod = m('cate');
			
			$query = [
				'fields' => 'id',
				'cond' => " type = 4 AND parent_id != 0 AND mark = 1 ",
				'order_by' =>'sort ASC , id DESC'
			];
			
			$tmp = $cateMod->getData($query);
			
			$data = [];
			if ( is_array($tmp) ) {
				foreach ( $tmp as $key => $val ) {
					$info = $cateMod->getInfo($val['id']);
					
					$data[] = [
						'cate_id' => $info['id'],
						'cate_name' => $info['name'],
					];
			    }
			}
			
			return message('操作成功', true ,$data);
		}
		
		
		
		/**
		 * @todo    获取就业邦学堂页面信息
		 * @author Malcolm  (2018年04月17日)
		 */
		public function getSchoolPageInfo($param,$userId){
			//广告
			$adList = m("ad")->getListByPosition('app_find_index_top');
			
			$_REQUEST['perpage'] = 3;
			
			$raidersIc = ic('raiders');
			false && $raidersIc = new Raiders();
			
			//攻略
			$param['type'] = 3;
			$strategyList = $raidersIc->getList($param,$userId,true);
			
			
			//秘籍
			$param['type'] = 1;
			$cheatsList = $raidersIc->getList($param,$userId,true);
			
			
			//试卷
			$param['type'] = 2;
			$testList = $raidersIc->getList($param,$userId,true);
			
			$data = [
				'ad_list' => $adList,
				'strategy_list' => $strategyList['list'],
				'cheats_list' => $cheatsList['list'],
				'test_list' => $testList['list'],
			];
			
			
			return message('操作成功', true ,$data);
		}
		
		
		
		/**
		 * @todo    获取附近的人
		 * @author Malcolm  (2018年04月17日)
		 */
		public function getNearbyPeople($param,$userId){
			$lat = trim($param['lat']);
			$lng = trim($param['lng']);
			
			if(!$lng || !$lat)
				return message('参数丢失');
			
			//从高德查找附近的人的ID
			import('gaode/yunTuBasic.lib');
			$yun = new YunTuBasic();
			
			$page = $perpage = $limit = 0;
			$this->initPage($page, $perpage, $limit);
			
			$tmp = $yun->getList($lng,$lat,' ',$page,$perpage,$userId);
			
			
			//如果获取到高德返回数据
			if($tmp['status'] == 1) {
				$ids = [];
				if ( is_array($tmp['datas']) ) {
					foreach ( $tmp['datas'] as $key => $val ) {
						//排除自己
						if($userId != $val['user_id'])
							$ids[] = $val['user_id'];
					}
				}
				
				
				$ids = implode(',' , $ids);
				
				$cond[] = " id in($ids)   ";
				
				$fields = 'id';
				$orderBy = "FIELD(`id`, $ids)";
			}else{
				//如果高德没有返回数据，则使用sql计算距离
				
				$fields = 'id,'.Zeus::getDisSql($lat,$lng);
				$orderBy = 'distance ASC';
				
				$radius = 5000;  //搜索半径 单位米
				
				$around = Zeus::getAround($lat,$lng,$radius);
				
				$lngMin = $around['lng']['min'];
				$lngMax = $around['lng']['max'];
				
				$latMin = $around['lat']['min'];
				$latMax = $around['lat']['max'];
				
				$cond[] = " `lng` BETWEEN '{$lngMin}' AND '{$lngMax}'  ";
				
				$cond[] = " `lat` BETWEEN '{$latMin}' AND '{$latMax}'  ";
			}
			
			
			$cond[] = " id !={$userId} AND status != 5 AND mark = 1 ";
			
			$query = [
				'fields' => $fields,
				'order_by' => $orderBy,
				'cond' => $cond,
				'limit' => $limit,
			];
			
			$userMod = m('user');
			
			$list = $userMod->getData($query);
			
			$count = $userMod->getNearbyCount($userId,$lat,$lng);
			
			$newList = [];
			
			
			
			if ( is_array($list) ) {
				foreach ( $list as $key => $val ) {
					$newList[] = $userMod->getNearbyListInfo($val['id'],$lat,$lng);
				}
			}
			
			$data = [
				'count'=>$count,
				'page'=>$page,
				'perpage'=>$perpage,
				'list'=>$newList
			];
			
			
			return message('操作成功', true ,$data);
			
		}
		
		
		
	}