<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/3/19
 * Time: 14:52
 */
class FunMod extends CBaseMod
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct('fun');
        $this->statusList=[
             [
                 'id'=>1,
                 'name'=>'待审核'
             ],[
                'id'=>2,
                'name'=>'审核通过'
             ],[
                'id'=>3,
                'name'=>'审核未通过'
             ]
            ];
    }
    /**
     * @todo    获取趣事信息
     * @author dingj  (2018年05月15日)
     */
    public function getFunInfo($id)
    {
        $info=parent::getInfo($id);
        $info['add_time'] = $info['add_time'] ? date('Y-m-d H:i:s', $info['add_time']) : '';//开始时间
        $info['city_name']= $this->cityMod->getCityName($info['dist_id']);
        //查询关联的用户
        $userInfo = $this->userMod->getInfo($info['user_id']);
        $info['username']=$userInfo['nickname'];

        //审核状态
        switch ($info['status']) {
            case '1':
                $info['status'] = "待审核";
                break;
            case '2':
                $info['status'] = "审核通过";
                break;
            case '3':
                $info['status'] = "审核未通过";
                break;
            default:
                $info['status'] = "待审核";
        }
        return $info;
     }


}