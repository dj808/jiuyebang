<?php

/**
 * 评论留言模型
 * Created by Malcolm.
 * Date: 2018/3/6  15:33
 */
class CommentMod extends CBaseMod {
            
    public function __construct() {
        parent::__construct('comment');
    }

    /**
     * @todo    获取详细信息
     * @author  Malcolm  (2018年04月14日)
     */
    public function getDetailInfo($id, $userId = 0,$param = []) {
        $info = parent::getInfo($id);
        if(!$info['id'])
            return false;
        $userInfo = $this->userMod->getInfo($info['user_id']);
        //是否作者回复
        $isAuthor = m('dynamic')->getInfo($info['type_id'])['user_id'] == $info['user_id']?1:2;
        //子级参数
        $child = [
            'level'      => $info['reply_level']+1,
            'reply_id'   => $info['user_id'],
            'reply_nickname' => $userInfo['nickname']
        ];
        //组装数据
        $data = [
            'comment_id'            => $info['id'] ,
            'comment_parent_id'     => $info['parent_id'] ,
            'comment_reply_level'   => $info['reply_level'] ,
            'comment_type'          => $info['type'] ,
            'comment_type_id'       => $info['type_id'] ,
            'comment_content'       => $info['content'] ,
            'comment_user_id'       => $info['user_id'] ,
            'comment_user_nickname' => $userInfo['nickname'] ,
            'comment_user_avatar'   => $userInfo['avatar'] ,
            'comment_add_date'      => Hera::humanDate($info['add_time']) ,
            'comment_is_author'     => $isAuthor,
            'comment_child_list'    => $this->getChild($id,$userId,$child),
        ];
        //回复的是谁
        if(!empty($param)&&$info['reply_level'] > 1){
            $data['comment_reply_id'] = $param['reply_id'];
            $data['comment_reply_nickname'] = $param['reply_nickname'];
        }
        //子留言数量
        if ($info['reply_level'] == 1) {
            //是否点赞
            $isPraise = m('praise')->isPraise($id , $userId , 2)? 2 : 1;
            $data['comment_is_praise']  = $isPraise ;
            $data['comment_praise_num'] = $info['praise_num'];
//            
//            $cond = "parent_id = {$info['id']} AND state <> 2 AND mark = 1";
//            $data['son_comment_num'] = $this->getCount($cond);
        }
        return $data;
    }


    /**
     * @todo    获取某一类型的评论列表
     * @author  Zhulx  (2018年07月23日)
     */
    public function getListByTypeId($typeId , $type = 1 ,$parentId = 0,$userId = 0) {
        $cond = " type = {$type} AND  type_id = {$typeId} AND state <>2 AND parent_id = {$parentId} AND mark = 1 ";

        $ids = $this->getIds($cond);

        $list = [];

        if (is_array($ids)) {
            foreach ($ids as $key => $val) {
                $list[] = $this->getDetailInfo($val,$userId);
            }
        }

        return $list;
    }
    
    /**
     * @todo    根据ID获取子集
     * @author  Zhulx (2018年07月19日)
     */
    public function getChild($id,$userId,$param){
        $comments = [];
        $childs = m('comment')->getData(['cond' => "parent_id = {$id} AND state <> 2 AND mark = 1 AND reply_level = {$param['level']}"]);
        foreach($childs as $child){
            $comments[] = m('comment')->getDetailInfo($child['id'],$userId,$param);
        }
        return $comments;
    }
    
    
    /**
     * @todo    同步评论点赞
     * @author Zhulx  (2018年07月20日)
     */
    public function syncPraise($id){
        if(!$id)
            return false;
        $count = m('praise')->getCount("type = 2 AND type_id = {$id} AND mark = 1");
        $this->edit(['praise_num' => $count],$id);
    }

}