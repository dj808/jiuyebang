<?php
	
	/**
	 * 分类模型
	 * Created by Zhulx.
	 * Date: 2018/7/19  14:11
	 */
	class TopicMod extends CBaseMod {
		
		public function __construct() {
			parent::__construct("topic");
		}
                
                /**
                 * @todo    获取列表信息
                 * @author  Zhulx 
                 */
                 public function getListInfo($id) {
                     $info = parent::getInfo($id);
                     $data = [
                         'topic_id'              => $info['id'] ,
                         'topic_title'           => $info['title'] ,
                         'topic_logo'            => $info['logo'] ,
                         'topic_introduce'       => $info['introduce'] ,
                         'topic_look_num'        => $info['look_num'] ,
                         'topic_comment_num'        => $info['look_num'] 
                     ];
                     return $data;
                 }
                 
                 /**
                * @todo    话题同步评论数
                * @author Zhulx  (2018年07月24日)
                */
                public function syncCommentNum($id){
                    if(!$id)
                        return false;
                    $dys= m('dynamic')->getData(["cond" => "topic_id = {$id} AND mark = 1"]);
                    $comment_num = 0;
                    foreach($dys as $dy){
                        $comment_num += $dy['comment_num'];
                    }
                    $this->edit(['comment_num' => $comment_num],$id);
                }
		
	}