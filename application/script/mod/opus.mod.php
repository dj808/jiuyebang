<?php

/**
 *  投票模型
 * Created by Malcolm.
 * Date: 2018/6/21  17:37
 */
class OpusMod extends CBaseMod {
    public function __construct() {
        parent::__construct('opus');

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
            'order_by' => 'vote_num Desc' ,
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