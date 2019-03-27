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
    public function getListInfo($id) {
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

    
   
    
}