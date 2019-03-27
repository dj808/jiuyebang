<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/3/29
 * Time: 14:52
 */
class cooperationApplyMod extends CBaseMod
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct('cooperation_apply');
        $this->statusList = [
            [
                'id'   => 1 ,
                'name' => '未完成'
            ] ,
            [
                'id'   => 2 ,
                'name' => '已选择完成'
            ]
        ];
    }
    /**
     * @todo    获取互助申请信息
     * @author dingj  (2018年05月15日)
     */
    public function getApplyInfo($id)
    {
        $info=parent::getInfo($id);

        //查询关联的用户
        $userInfo = $this->userMod->getInfo($info['user_id']);
        $info['username']=$userInfo['nickname'];
        //被申請的用戶
        $userInfo = $this->userMod->getInfo($info['to_user_id']);
        $info['to_username']=$userInfo['nickname'];
        //查询关联的互助信息
        $cooperInfo = $this->cooperationMod->getInfo($info['cooper_id']);
        $info['coopername']=$cooperInfo['title'];

        $info['status']=$info['status']==1?'未完成' : '已选择完成';

        $info['add_time'] = $info['add_time'] ? date('Y-m-d H:i:s', $info['add_time']) : '';//添加时间

       return $info;
     }


}