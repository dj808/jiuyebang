<?php


/**
 *  新趣事拓展控制器
 * Created by Malcolm.
 * Date: 2018/6/20  10:44
 */
class Dynamic extends Zeus {
    public $mod;
    public  function __construct() {
        parent::__construct("dynamic");

        $this->mod = m('dynamic');
        false&&$this->mod = new DynamicMod();
        
        $this->topicMod = m('topic');
        false&&$this->topicMod = new TopicMod();
    }


    /**
     * @todo    发布趣事
     * @author Malcolm  (2018年06月20日)
     */
    public function pushDynamic($param , $userId){
        $content = trim($param['content']);

        $lng = $param['lng'];
        $lat = $param['lat'];
        $adcode = $param['adcode'];

        //$images = json_decode($param['imgs'],true);

        $tmpArr = Hera::uploads('imgs');

        $images = [];
        if ($tmpArr && is_array($tmpArr)) {
            foreach ($tmpArr as $key => $val) {
                $images[] = [
                    'url' => $val,
                ];
            }
        }

        if(!$content && !count($images))
            return message('请输入内容或上传图片');

        if(!$lat || !$lng || !$adcode)
            return message('参数丢失');
        
        //2018.7.19(文章所属话题)迭代 @Zhulx
        $topics = $this->topicMod->getData(['cond' => ['is_show = 1','mark = 1']]);
        if($topics){
            preg_match_all('/#([^#]*?)#/',$content,$matches);
            $topic_id = 0;
            foreach($topics as $topic){
                if(in_array($topic['title'],$matches[0])){
                    $topic_id = $topic['id'];
                }
            }
        }
        $data = [
            'user_id' => $userId,
            'content' => $content,
            'imgs' => serialize($images),
            'lng' => $lng,
            'lat' => $lat,
            'adcode' => $adcode,
            'topic_id' => $topic_id
        ];

        $rs = $this->mod->edit($data);

        if(!$rs)
            return message('系统繁忙，请稍候再试');

        return message('操作成功', true );
    }


    /**
     * @todo    获取趣事列表
     * @author Zhulx  (2018年07月23日)
     */
    public function getList($param,$userId,$isIn = false){
        //趣事列表
        $cond[] = 'mark = 1';
        $page = $perpage = $limit = 0;
        $this->initPage($page, $perpage, $limit);
        $query = [
            'fields' => 'id',
            'cond' => $cond,
            'limit' => $limit,
            'order_by' => ' id DESC ',
        ];
        $list = $this->mod->getData($query);
        $count = $this->mod->getCount($query['cond']);
        $newList = [];
        if ( is_array($list) ) {
            foreach ( $list as $key => $val ) {
                $newList[] = $this->mod->getListInfo($val['id'],$userId);
            }
        }
        //热门话题(4条)
        $topics = $this->topicMod->getData(['fields' => 'id','cond' => 'is_show = 1 AND mark = 1','limit' => 4,'order_by' => ' look_num DESC']);
        $topicList = [];
        if ( is_array($list) ) {
            foreach ( $topics as $topic ) {
                $topicList[] = $this->topicMod->getListInfo($topic['id']);
            }
        }
        $data = [
            'count'=>$count,
            'page'=>$page,
            'perpage'=>$perpage,
            'list'=>$newList,
            'topic_list'=>$topicList 
        ];

        if($isIn)
            return $data;


        return message('操作成功', true ,$data);
    }
    
    /**
     * @todo    获取校园趣事详情
     * @author Zhulx  (2018年07月23日)
     */
    public function getDynamicInfo($param,$userId){
        $dynamicId = intval($param['dynamic_id']);
        if(!$dynamicId)
            return message('参数丢失');
        $info = $this->mod->getListInfo($dynamicId,$userId);
        //评论分页
        $page = $perpage = $limit = 0;
        $this->initPage($page, $perpage, $limit);
        $query = [
            'fields' => 'id',
            'cond' => "type = 7 AND  type_id = {$dynamicId} AND state <> 2 AND parent_id = 0 AND mark = 1",
            'limit' => $limit,
            'order_by' => ' id DESC ',
        ];
        $list = m('comment')->getData($query);
        $count = m('comment')->getCount($query['cond']);
        //评论列表
        $newList = [];
        if ( is_array($list) ) {
            foreach ( $list as $val ) {
                $newList[] = m('comment')->getDetailInfo($val['id'],$userId);
            }
        }
        $data = [
            'info'=>$info,
            'comment_list' =>[
                'count'=>$count,
                'page'=>$page,
                'perpage'=>$perpage,
                'list' => $newList
            ]
        ];
        
        //自动维护查看次数
        $this->mod->editColumnValue($dynamicId,'look_num');
        if($info['topic_id'])
            $this->topicMod->editColumnValue($info['topic_id'],'look_num');
        return message('操作成功', true ,$data);
    }
    
    /**
     * @todo    获取话题列表
     * @author Zhulx  (2018年07月23日)
     */
    public function getTopicList($param){
        $search = $param['search'];
        if($search){
            $cond[] = "title LIKE '%{$search}%'";
        }
        $cond[] = 'is_show = 1 AND mark = 1';
        $page = $perpage = $limit = 0;
        $this->initPage($page, $perpage, $limit);
        $query = [
            'fields' => 'id,look_num',
            'cond' => $cond,
            'limit' => $limit,
            'order_by' => ' look_num DESC ',
        ];
        $list = $this->topicMod->getData($query);
        $count = $this->topicMod->getCount($query['cond']);
        $topicList = [];
        if ( is_array($list) ) {
            foreach ( $list as $key => $val ) {
                $topicList[] = $this->topicMod->getListInfo($val['id']);
            }
        }

        $data = [
            'count'=>$count,
            'page'=>$page,
            'perpage'=>$perpage,
            'list'=>$topicList
        ];

        return message('操作成功', true ,$data);
    }

    
    /**
     * @todo    获取话题详情
     * @author Zhulx  (2018年07月24日)
     */
    public function getTopicInfo($param,$userId){
        $topicId = $param['topic_id'];
        if(!$topicId)
            return message('参数丢失');
        //话题详情
        $info = $this->topicMod->getListInfo($topicId,$userId);
        //热门取第一条
        $query2 = [
            'fields' => 'id,praise_num',
            'cond' => "topic_id = {$topicId} AND mark = 1",
            'limit' => '0,1',
            'order_by' => ' praise_num DESC ',
        ];
        $hot_id = $this->mod->getData($query2)[0]['id'];
        $hot_dynamic = $this->mod->getListInfo($hot_id,$userId);
        //关联话题的趣事
        $page = $perpage = $limit = 0;
        $this->initPage($page, $perpage, $limit);
        $query = [
            'fields' => 'id,look_num',
            'cond' => "topic_id = {$topicId} AND mark = 1 AND id <> {$hot_id}",
            'limit' => $limit,
            'order_by' => ' look_num DESC ',
        ];
        $list = $this->mod->getData($query);
        $count = $this->mod->getCount($query['cond']);
        $dynamicList = [];
        if ( is_array($list) ) {
            foreach ( $list as $key => $val ) {
                $dynamicList[] = $this->mod->getListInfo($val['id'],$userId);
            }
        }
        $data = [
            'info' => $info,
            'hot_dynamic' => $hot_dynamic,
            'dynamic_list' =>[
                'count'=>$count,
                'page'=>$page,
                'perpage'=>$perpage,
                'list' => $dynamicList
            ]
        ];
        //自动维护查看次数
        $this->topicMod->editColumnValue($topicId,'look_num');
        return message('操作成功', true ,$data);
    }
    
    /**
    * @todo    获取个人主页
    * @author Zhulx  (2018年07月24日)
    */
    public function getHomePage($param,$userId){
        $otherId = $param['other_id'];
        if(!$otherId)
            return message('参数错误');
        //分页
        $page = $perpage = $limit = 0;
        $this->initPage($page, $perpage, $limit);
        $query = [
            'fields' => 'id',
            'cond' => "user_id = {$otherId} AND mark = 1",
            'limit' => $limit,
            'order_by' => ' id DESC ',
        ];
        $list = $this->mod->getData($query);
        $count = $this->mod->getCount($query['cond']);   
        $newList = [];
        if ( is_array($list) ) {
            foreach ( $list as $key => $val ) {
                $newList[] = $this->mod->getListInfo($val['id'],$userId);
            }
        }
        //用户信息
        $userInfo = m('user')->getFewInfo($otherId,$userId);
        $userInfo['article_num'] = $count;
        $data = [
            'info' => $userInfo,
            'dynamic_list' => 
                [
                    'count'=>$count,
                    'page'=>$page,
                    'perpage'=>$perpage,
                    'list'=>$newList,
                ]
        ];
        return message('操作成功', true ,$data);
    }
    
    /**
    * @todo    好友列表
    * @author Zhulx  (2018年07月25日)
    */
    public function getFriendMore($param,$userId,$isIn = true){
        $type = $param['type'];
        $search = $param['search'];
        //分页
        $page = $perpage = $limit = 0;
        $this->initPage($page, $perpage, $limit);
        //有分类ID
        if($type){
            //已关注的人
            $noList = m('follow')->getSingle(['cond' => "user_id = {$userId}"],'follow_user_id');
            if($noList){
                $cond[] = "id NOT IN ({$noList})";
            }
            $cond[] = "type = 1 AND mark = 1  AND id <> {$userId}";
            $format = '';
        }
        if($search){
            $cond[] = "nickname LIKE '%{$search}%' AND mark = 1";
        }
        $order_by = ' fans DESC';
        switch ($type){
            //推荐
            case 1:
                $cond[] = " fans <> 0";
                break;
            //猜你喜欢
            case 2:
                //根据相同点赞查找
                $typeId = $praise = m('praise')->getSingle(['cond' => "type = 2 AND mark = 1 AND user_id = {$userId}"],'type_id');
                if($typeId){
                    $parIds = m('praise')->getSingle(['fields'=> 'DISTINCT user_id','cond' => "type_id IN ({$typeId}) AND type = 2 AND mark = 1"],'user_id');
                    $cond[] = "id IN ({$parIds}) ";
                    $cond[] = " fans <> 0";
                }else{
                    $cond[] = " follow <> 0";
                    $order_by = 'follow DESC';
                }
                break;
            //附近的人
            case 3:
                $userInfo = m('user')->getInfo($userId);
                $ar = Zeus::getAround($userInfo['lat'], $userInfo['lng'],5000);
                $cond[] = "lat >".$ar['lat']['min'] ." AND lat <".$ar['lat']['max'];
                $cond[] = "lng >".$ar['lng']['min'] ." AND lng <".$ar['lng']['max'];
                $cond[] = "lat <> 0 AND lng <> 0";
                $format = 'distance';
                $order_by = ' id DESC';
                break;
            //可能认识的人
            case 4:
                $foIds = m('follow')->getSingle(["fields" => "DISTINCT follow_user_id","cond" => "user_id IN ({$noList}) AND follow_user_id <> {$userId}"],'follow_user_id');
                $cond[] = "id IN ({$foIds})";
                $format = 'possible';
                $order_by = ' id DESC';
                break;
        }
        if(!$isIn){
            $limit = '0,3';
        }
        $query = [
            'fields' => 'id,follow,fans',
            'cond' => $cond,
            'limit' => $limit,
            'order_by' => $order_by
        ];
        $users = m('user')->getData($query);
        $count = m('user')->getCount($query['cond']);
        $userList = [];
        if ( is_array($users) ) {
            foreach ( $users as $user ) {
                $userList[] = m('user')->getFewInfo($user['id'],$userId,$format);
            }
        }
        if(!$isIn)
            return $userList;
        
            
        $data = [
            'count'=>$count,
            'page'=>$page,
            'perpage'=>$perpage,
            'list'=>$userList,
        ];
        return message('操作成功', true ,$data);
        
    }
    
    /**
    * @todo    加好友
    * @author Zhulx  (2018年07月27日)
    */
    public function getFriend($param,$userId){
        $data['recommend_list'] = $this->getFriendMore(['type' => 1], $userId,false);
        $data['like_list'] = $this->getFriendMore(['type' => 2], $userId,false);
        $data['distance_list'] = $this->getFriendMore(['type' => 3], $userId,false);
        $data['possible_list'] = $this->getFriendMore(['type' => 4], $userId,false);
        return message('操作成功', true ,$data);
    }
    
    /**
    * @todo   趣事增加分享数
    * @author Zhulx  (2018年07月26日)
    */
     public function addShareNum($param){
        $dynamicId = $param['dynamic_id'];
        if(!$dynamicId)
            return message('参数丢失');
        //维护分享数
        $this->mod->editColumnValue($dynamicId,'share_num');
        $data = $this->mod->getListInfo($dynamicId);
        return message('操作成功', true ,['share_num' => $data['share_num']]);
    }
}