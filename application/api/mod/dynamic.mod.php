<?php

/**
 *  新趣事模型(迭代)
 * Created by Zhulx.
 * Date: 2018/7/20  14:01
 */
class DynamicMod extends CBaseMod {
    public function __construct() {
        parent::__construct('dynamic');

    }


    /**
     * @todo    获取列表信息)
     * @author  Zhulx 
     */
    public function getListInfo($id , $userId = 0) {
        $info = parent::getInfo($id);

        //图片列表
        if (!trim($info['imgs']))
            $images = [];
        else
            $images = unserialize($info['imgs']);


        $imagesSmall = [];
        if (is_array($images)) {
            foreach ($images as $key => $val) {
                $imagesSmall[$key]['url'] = $val['url'] . '?x-oss-process=image/resize,w_160,limit_0';
            }
        }

        if(!count($imagesSmall))
            $imagesSmall = new stdClass();


        if(!$images)
            $images = new stdClass();

        if(!count($images))
            $images = new stdClass();


        //是否点赞
        $isPraise = $this->praiseMod->isPraise($id , $userId , 1)? 2 : 1;
        
        //是否关注 
        $isFollow = $this->followMod->isFollow($userId,$info['user_id'])? 2 : 1;
        
        //发布者信息
        $userInfo = $this->userMod->getInfo($info['user_id']);


        $data = [
            'dynamic_id'            => $info['id'] ,
            'publish_user_id'       => $info['user_id'] ,
            'publish_user_avatar'   => $userInfo['avatar'] ,
            'publish_user_nickname' => $userInfo['nickname'] ,
            'content'               => $info['content'] ,
            'images_list'           => $images ,
            'thumbnail_list'        => $imagesSmall ,
            'is_follow'             => $isFollow ,
            'is_praise'             => $isPraise ,
            'praise_num'            => $info['praise_num'] ,
            'comment_num'           => $info['comment_num'] ,
            'share_num'             => $info['share_num'],
            'look_num'              => $info['look_num'],
            'topic_id'              => $info['topic_id'],
            'topic_title'            => m('topic')->getInfo($info['topic_id'])['title'],
            'add_time'              => Hera::humanDate($info['add_time']) ,
            'share_url'             => WAP_URL . '/?app=share&act=dynamic&id=' . $info['id']
        ];

        return $data;
    }

    
    /**
     * @todo    同步趣事点赞
     * @author Zhulx  (2018年07月20日)
     */
    public function syncPraise($id){
        if(!$id)
            return false;
        $count = m('praise')->getCount("type = 1 AND type_id = {$id} AND mark = 1");
        $this->edit(['praise_num' => $count],$id);
    }

    /**
     * @todo    趣事同步评论数
     * @author Zhulx  (2018年07月23日)
     */
    public function syncCommentNum($id){
        if(!$id)
            return false;
        $count = m('comment')->getCount("type = 7 AND type_id = {$id} AND mark = 1");
        $this->edit(['comment_num' => $count],$id);
    }
    
}