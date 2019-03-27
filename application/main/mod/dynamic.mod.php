<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/4/27
 * Time: 14:51
 */

class DynamicMod extends CBaseMod
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct('dynamic');
    }
     /**
     * @todo    获取校园新鲜事信息
     * @author dingj  (2018年05月15日)
     */
    public function getListInfo($id)
    {
        $info=parent::getInfo($id);
        $info['add_time'] = $info['add_time'] ? date('Y-m-d H:i:s', $info['add_time']) : '';//开始时间
        $info['city_name']= $this->cityMod->getCityName($info['dist_id']);
        //查询关联的用户
        $userInfo =$this->userMod->getInfo($info['user_id']);
        $info['username']=$userInfo['nickname'];
        $info['source_type'] = $info['source_type'] == 1 ? 'App添加':'后台添加';

        return $info;
    }


}