<?php

/**
 *  投票作品控制器
 * Created by Malcolm.
 * Date: 2018/6/21  00:29
 */
class OpusMod extends CBaseMod {
    public function __construct() {
        parent::__construct('opus');

    }


    /**
     * @todo    获取列表信息
     * @author  Malcolm  (2018年06月21日)
     */
    public function getListInfo($id) {
        $info = parent::getInfo($id);

	    //计算投票数
	    $cond = " opus_id = {$info['id']} AND user_id > 0 AND mark = 1 ";

	    $info['vote_num'] = $this->opusVoteRelMod->getCount($cond);

        $data = [
            'id'       => $info['id'] ,
            'title'    => $info['title'] ,
            'school'   => $info['school'] ,
            'look_num' => $info['look_num'] ,
            'vote_num' => $info['vote_num'] ,
        ];

        return $data;
    }


}