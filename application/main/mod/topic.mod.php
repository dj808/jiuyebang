<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/4/27
 * Time: 14:51
 */

class topicMod extends CBaseMod
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct('topic');
    }
     /**
     * @todo    获取话题信息
     * @author dingj  (2018年05月15日)
     */
    public function getListInfo($id)
    {
        $info=parent::getInfo($id);
        $info['is_show'] = $info['is_show'] == 1 ? '是':'否';

        return $info;
    }


}