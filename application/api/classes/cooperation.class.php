<?php
	
	/**
	 * 互助控制器拓展
	 * Created by Malcolm.
	 * Date: 2018/4/14  14:15
	 */
	class Cooperation extends Zeus {
		public $mod;
		public  function __construct() {
			parent::__construct("cooperation");
			
			$this->mod = m('cooperation');
			false&&$this->mod = new CooperationMod();
		}
		
		
		/**
		 * @todo    获取列表
		 * @author Malcolm  (2018年04月14日)
		 */
		public function getList($param,$userId,$isIn=false){
			$type = intval($param['type']);
			if(!$type)
				return message('参数丢失');
			
			$cond[] = " type = {$type} AND mark = 1 ";
			
			//按距离排序
			$lng = $param['lng'];
			$lat = $param['lat'];
			
			if($lat && $lng){
				$fields = 'id,'.Zeus::getDisSql($lat,$lng);
				$orderBy = 'distance ASC , id DESC';
			}else{
				$fields = 'id';
				$orderBy = 'add_time DESC';
			}
			
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
			
			$page = $perpage = $limit = 0;
			$this->initPage($page, $perpage, $limit);
			
			$query = [
				'fields' => $fields,
				'order_by' => $orderBy,
				'cond' => $cond,
				'limit' => $limit,
			];
			
			$list = $this->mod->getData($query);
			$count = $this->mod->getCount($query['cond']);
			
			$newList = [];
			
			if ( is_array($list) ) {
				foreach ( $list as $key => $val ) {
					$newList[] = $this->mod->getListInfo($val['id'],$lat,$lng);
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
		 * @todo    获取转让、互助详情
		 * @author Malcolm  (2018年04月14日)
		 */
		public function getCooperationInfo($param,$userId){
			$id = intval($param['cooperation_id']);
			if(!$id)
				return message('参数丢失');
			
			$info = $this->mod->getDetailInfo($id);
			if(!$info['cooperation_title'])
				return message('改信息已过期');
			
			//自动维护查看次数
			$this->mod->editColumnValue($id,'look_num');
			
			//是否已经申请
			if($userId){
				$cond = " cooper_id = {$id} AND user_id = {$userId} AND status = 1 AND mark = 1 ";
				$count = m('cooperationApply')->getCount($cond);
				
				$info['is_apply'] = $count?1:2;
			}else{
				$info['is_apply'] = 2;
			}
			
			//留言
			$param['type'] = 5;
			$param['type_id'] = $id;
			$param['parent_id'] = 0;
			
			$info['comment_list'] = ic('comment')->getList($param,$userId,true);
			
			return message('操作成功', true ,$info);
		}
		
		
		/**
		 * @todo    申请互助/转让
		 * @author Malcolm  (2018年04月14日)
		 */
		public function serApplyCooperation($param,$userId){
			$id = intval($param['cooperation_id']);
			if(!$id)
				return message('参数丢失');
			
			$info = $this->mod->getInfo($id);
			
			switch ($info['type']){
				case 1:
					$name = '转让';
					break;
					
				case 2:
					$name = '互助';
					break;
					
				default :
					$name = '转让';
					break;
					
			}
			
			//判断是否已经申请
			$applyMod = m('cooperationApply');
			
			$cond = " cooper_id = {$id} AND user_id = {$userId} AND status = 1 AND mark = 1 ";
			$count = $applyMod->getCount($cond);
			
			if($count)
				return message('您已申请过，无法重复申请');
			
			//判断是否自己申请
			if($info['user_id'] == $userId)
				return message("您无法申请自己发布的{$name}");
			
			
			$data = [
				'cooper_id' => $id,
				'user_id' => $userId,
				'to_user_id' => $info['user_id'],
				'snap' => serialize($info),
			];
			
			$rs = $applyMod->edit($data);
			if(!$rs)
				return message('系统繁忙，请稍候再试');
			
			//发送通知
			Zeus::sendMsg([
				'type' =>['msg','push'],
				'user_id' =>$userId,
				'title' =>"申请{$name}成功",
				'content' =>"您已成功申请{$name}【{$info['title']}】！重要提示：所有的付费和收费都是线下行为，请自行与发布人协商完成！",
				'msg_type' =>1,
				'user_type' =>1,
			]);
			
			return message('操作成功', true );
		}
		
		
		
		
	}