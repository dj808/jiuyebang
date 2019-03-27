<?php

/**
 *  关注模型
 * Created by Zhulx.
 * Date: 2018/7/20  13:39
 */
class FollowMod extends CBaseMod {
    public function __construct() {
        parent::__construct('follow');

    }

    /**
     * @todo    获取该用户粉丝列表
     * @author Zhulx  (2018年07月24日)
     */
    public function getFans($otherId,$userId,$limit){
        $query = [
            'cond' => "follow_user_id = {$otherId} AND mark = 1 ",
            'limit' => $limit,
            'order_by' => ' id DESC ',
        ];
        $fans = $this->getData($query);
        $count = $this->getCount($query['cond']);
        $fansList = [];
        if ( is_array($fans) ) {
            foreach($fans as $key => $fan){
                $fansList[$key] = m('user')->getFewInfo($fan['user_id']);
                //是否已关注或本人
                $fansList[$key]['is_follow'] = $fan['user_id'] == $userId ? 3 :($this->isFollow($userId,$fan['user_id']) ? 2 : 1);
            }
        }
        $data = [
            'count'=>$count,
            'list' => $fansList
        ];
        return $data;
    }
    
    /**
     * @todo    获取该用户关注列表
     * @author Zhulx  (2018年07月24日)
     */
    public function getFollow($otherId,$userId,$limit){
        $query = [
            'cond' => "user_id = {$otherId} AND mark = 1 ",
            'limit' => $limit,
            'order_by' => ' id DESC ',
        ];
        $follows = $this->getData($query);
        $count = $this->getCount($query['cond']);
        $followList = [];
        if ( is_array($follows) ) {
            foreach($follows as $key => $follow){
                $followList[$key] = m('user')->getFewInfo($follow['follow_user_id']);
                //是否已关注或本人
                $fansList[$key]['is_follow'] = $follow['follow_user_id'] == $userId ? 3 :($this->isFollow($userId,$follow['follow_user_id']) ? 2 : 1);
            }
        }
        $data = [
            'count'=>$count,
            'list' => $followList
        ];
        return $data;
    }

    /**
     * @todo    是否已关注
     * @author Zhulx  (2018年07月20日)
     */
    public function isFollow($self , $otherId){
        if(!$self)
            return false;
        $cond = " user_id = {$self} AND  follow_user_id = {$otherId} AND mark = 1 ";

        $id = $this->getData(['cond' => $cond])[0]['id'];

        return $id?:false;
    }


}