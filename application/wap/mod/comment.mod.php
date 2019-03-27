<?php
	
	/**
	 * 评论留言模型
	 * Created by Malcolm.
	 * Date: 2018/3/6  15:33
	 */
	class CommentMod extends CBaseMod {
		public function __construct (  ) {
			parent::__construct('comment');
		}
		
		/**
		 * @todo    获取详细信息
		 * @author Malcolm  (2018年04月14日)
		 */
		public function getDetailInfo ( $id ) {
			$info =  parent::getInfo($id);
			
			$userInfo = $this->userMod->getInfo($info['user_id']);
			
			
			
			$data = [
				'comment_id' =>  $info['id'],
				'comment_parent_id' =>  $info['parent_id'],
				'comment_reply_level' =>  $info['reply_level'],
				'comment_type' =>  $info['type'],
				'comment_type_id' =>  $info['type_id'],
				'comment_content' =>  $info['content'],
				'comment_user_id' =>  $info['user_id'],
				'comment_user_nickname' =>  $userInfo['nickname'],
				'comment_user_avatar' =>  $userInfo['avatar'],
				'comment_add_date' =>  Hera::humanDate($info['add_time']),
                                'comment_praise_num' =>  $info['praise_num'],
			];
			
			//子留言数量
			if($info['reply_level']==1){
				$cond = "parent_id = {$info['id']} AND state <> 2 AND mark = 1";
				$data['son_comment_num'] = $this->getCount($cond);
			}
			
			return $data;
		}
		
		
	}