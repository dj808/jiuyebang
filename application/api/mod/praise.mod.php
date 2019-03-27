<?php

/**
 *  点赞模型
 * Created by Malcolm.
 * Date: 2018/6/20  14:47
 */
class PraiseMod extends CBaseMod {
    public function __construct() {
        parent::__construct('praise');

    }


    /**
     * @todo    获取某一类型的点赞列表
     * @author Malcolm  (2018年06月20日)
     */
    public function getListByTypeId($typeId,$type=1){
        $cond = " type = {$type} AND  type_id = {$typeId} AND mark = 1 ";

        $ids = $this->getIds($cond);

        $list = [];

        if (is_array($ids)) {
            foreach ($ids as $key => $val) {
                $list[] = $this->getOwnUserInfo($val);
            }
        }


        return $list;
    }



    /**
     * @todo    获取点赞用户列表信息
     * @author Malcolm  (2018年06月20日)
     */
    public function getOwnUserInfo($id){
        $info = parent::getInfo($id);

        $userInfo = $this->userMod->getInfo($info['user_id']);


        $data = [
            'praise_user_id' => $info['user_id'],
            'praise_user_nickname' => $userInfo['nickname'],
            'praise_user_avatar' => $info['avatar'],
        ];

        return $data;
    }


    /**
     * @todo    是否已点赞
     * @author Zhulx  (2018年07月23日)
     */
    public function isPraise($typeId,$userId,$type=1){
        if(!$userId)
            return false;
        $cond = " type = {$type} AND  type_id = {$typeId} AND user_id = {$userId} AND mark = 1 ";
        $id = $this->getData(['cond' => $cond])[0]['id'];
        return $id?:false;
    }


}