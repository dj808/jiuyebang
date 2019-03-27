<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/3/29
 * Time: 14:52
 */
class systemMessageMod extends CBaseMod
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct('system_message');
    }
    /**
     * @todo    获取系统信息
     * @author dingj  (2018年05月15日)
     */
    public function getListInfo($id)
    {
        $info=parent::getInfo($id);
            //查询关联的用户
            $userInfo =$this->userMod->getInfo($info['user_id']);
            $info['user_id']=$userInfo['nickname'];
            $info['type']=$info['type']==1 ? '用户' : '后台管理员';
            $info['msg_type']=$info['msg_type']==1 ? '单条消息' : '群发消息';
            $info['add_time'] = $info['add_time'] ? date('Y-m-d H:i:s', $info['add_time']) : '';//添加时间
        return $info;
     }


}