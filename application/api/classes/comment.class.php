<?php
	
	/**
	 * 拼接留言控制器拓展
	 * Created by Malcolm.
	 * Date: 2018/4/14  15:29
	 */
	class Comment extends Zeus {
		public $mod;
		public  function __construct() {
			parent::__construct("comment");
			
			$this->mod = m('comment');
			false&&$this->mod = new CommentMod();
		}
		
		
		/**
		 * @todo    获取留言/评论列表
		 * @author Malcolm  (2018年04月14日)
		 */
		public function getList($param,$userId,$isIn=false){
			$type = intval($param['type']);
			$typeId = intval($param['type_id']);
			
			$parentId = intval($param['parent_id']);
			
			if(!$type|| !$typeId)
				return message('参数丢失');
			
			if($parentId)
				$cond[] = " parent_id = {$parentId} ";
			else
				$cond[] = " parent_id = 0 ";
			
			$cond[] = " type = {$type} AND type_id = {$typeId} AND state <> 2 AND  mark = 1 ";
			
			$data = $this->pageData([
				'cond'=>$cond,
				'order_by' =>' id DESC'
			],$this->mod,'getDetailInfo');
			
			if($isIn)
				return $data;
			
			return message('操作成功', true ,$data);
		}
		
		
		/**
		 * @todo    发布留言
		 * @author Zhulx  (2018年07月23日)
		 */
		public function setComment($param,$userId){
			$type = intval($param['type']);
			$typeId = intval($param['type_id']);
			$parentId = intval($param['parent_id'])?:0;
			$reply_level = 1;
                        if($parentId){
                            $reply_level = $this->mod->getInfo($parentId)['reply_level'] + 1;
                        }
			$content = trim($param['content']);
			
			if(!$type || !$typeId)
				return message('参数丢失');
			
			if(!$content)
				return message('请输入内容');
			
			$data = [
				'parent_id' =>$parentId,
				'reply_level' =>$reply_level,
				'type' =>$type,
				'type_id' =>$typeId,
				'user_id' =>$userId,
				'content' =>$content,
			];
			
			$commentId = $this->mod->edit($data);
			if(!$commentId)
				return message('系统繁忙，请稍候再试');
			
			//发布通知
			if($parentId){
				switch ($type){
					case 1:
						$name = '早读分享';
						break;
					
					case 2:
						$name = '秘籍';
						break;
					
					case 3:
						$name = '攻略';
						break;
					
					case 4:
						$name = '试卷';
						break;
					
					case 5:
						$name = '互助';
						break;
					case 7:
						$name = '趣事';
						break;
                                            
					default :
						$name = '早读分享';
				}
				
				//获取父级的用户ID
				$commentInfo = $this->mod->getInfo($parentId);
				
				Zeus::sendMsg([
					'type' =>['msg','push'],
					'user_id' =>$commentInfo['user_id'],
					'title' =>"您有新的评论回复",
					'content' =>"您在{$name}下的评论有新的回复哦，快去看看吧！",
					'msg_type' =>1,
					'user_type' =>1,
				]);
				
			}

            if($type==7){
                //同步趣事评论数
                m('dynamic')->syncCommentNum($typeId);
                //同步话题评论数
                $topicId = m('dynamic')->getInfo($typeId)['topic_id'];
                m('topic')->syncCommentNum($topicId);
                $data = m('comment')->getDetailInfo($commentId,$userId);
                $pId = m('comment')->getInfo($param['parent_id'])['user_id'];
                if($data['comment_reply_level'] > 1){
                    $userInfo = m('user')->getInfo($pId);
                    $data['comment_reply_id'] = $userInfo['id'];
                    $data['comment_reply_nickname'] = $userInfo['nickname'];
                }
                return message('操作成功', true ,$data);
            }
			
			return message('操作成功', true );
		}
		
		
	}