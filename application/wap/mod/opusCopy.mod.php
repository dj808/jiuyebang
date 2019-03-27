<?php

/**
 *  投票模型
 * Created by Malcolm.
 * Date: 2018/6/21  17:37
 */
class OpusCopyMod extends CBaseMod {
    public function __construct() {
        parent::__construct('opus_copy');

    }

    public function getInfo($id) {
        $info = parent::getInfo($id);

        $info['detail'] = htmlspecialchars_decode($info['detail']);

        $info['share_title'] = '互联网+ 大赛投票作品：'.$info['title'];

        $info['share_url'] = WAP_URL . '/?app=opus&act=share&id=' . $info['id'];

        $info['share_img_url'] = $info['cover'];

        $info['share_describe'] = "我正在参加江苏省互联网+创业大赛，{$info['id']}号作品：【{$info['title']}】需要您宝贵的一票！";

        return $info;
    }

    /**
     * @todo    根据老题号，获取新ID
     * @author  Malcolm  (2018年06月21日)
     */
    public function getIdByOldId($oldId) {
        $cond = " old_num = {$oldId} AND mark = 1 ";

        $ids = $this->getIds($cond);

        return $ids[0] ? $ids[0] : 0;
    }


    /**
     * @todo    获取列表信息
     * @author  Malcolm  (2018年06月21日)
     */
    public function getListInfo($id) {
        $info = parent::getInfo($id);

        $data = [
            'id'       => $info['id'] ,
            'title'    => $info['title'] ,
            'school'   => $info['school'] ,
            'cover'    => $info['cover'] ,
            'look_num' => $info['look_num'] ,
            'vote_num' => $info['vote_num'] ,
        ];

        return $data;
    }

    /**
     * @todo    按投票数排序
     * @author  Malcolm  (2018年06月21日)
     */
    public function getEndList() {
        $query = [
            'fields'   => 'id' ,
            'order_by' => 'vote_num DESC' ,
            'cond'     => 'mark = 1'
        ];

        $rs = $this->getData($query);

        $list = [];
        if (is_array($rs)) {
            foreach ($rs as $key => $val) {
                $list[] = $this->getListInfo($val['id']);
            }
        }

        return $list;
    }


    /**
     * @todo    获取列表（正常排序）
     * @author Malcolm  (2018年06月21日)
     */
    public function getList(){
        $query = [
            'fields'   => 'id' ,
            'order_by' => 'id ASC' ,
            'cond'     => 'mark = 1'
        ];

        $rs = $this->getData($query);

        $list = [];
        if (is_array($rs)) {
            foreach ($rs as $key => $val) {
                $list[] = $this->getListInfo($val['id']);
            }
        }

        return $list;
    }


}