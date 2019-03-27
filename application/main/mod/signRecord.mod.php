<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/3/29
 * Time: 14:52
 */
class signRecordMod extends CBaseMod
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct('sign_record');
    }
    /**
     * @todo    获取用户签到记录信息
     * @author dingj  (2018年05月15日)
     */
    public function getListInfo($id)
    {
        $info=parent::getInfo($id);
        //查询关联的用户
        $userInfo = $this->userMod->getInfo($info['user_id']);
        $info['user_id']=$userInfo['nickname'];
        $info['type']=$info['type']==1 ? '普通签到' : '连续签到奖励';
        $info['add_time'] = $info['add_time'] ? date('Y-m-d H:i:s', $info['add_time']) : '';//添加时间
        return $info;
     }


}