<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/3/29
 * Time: 14:52
 */
class userDigitalMod extends CBaseMod
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct('user_digital_rel');
    }
    /**
     * @todo    获取用户积分/成长值 记录信息
     * @author dingj  (2018年05月15日)
     */
    public function getListInfo($id)
    {
        $info=parent::getInfo($id);
        //查询关联的用户
        $userInfo =$this->userMod->getInfo($info['user_id']);
        $info['user_id']=$userInfo['nickname'];
        $info['classify']=$info['classify']==1 ? '积分' : '成长值';
        $info['add_time'] = $info['add_time'] ? date('Y-m-d H:i:s', $info['add_time']) : '';//添加时间

        if($info['classify']==1){
            switch ($info['type']) {
                case '1':
                    $info['type'] = "消费获得";
                    break;
                case '2':
                    $info['type'] = "邀请";
                    break;
                case '3':
                    $info['type'] = "积分消费";
                    break;
                default:
                    $info['type'] = "消费获得";
            }
        }else{
              switch ($info['type']) {
                 case '1':
                     $info['type'] = "签到";
                     break;
                 case '2':
                     $info['type'] = "兼职";
                    break;
                 case '3':
                    $info['type'] = "培训";
                    break;
                 case '4':
                    $info['type'] = "邀请";
                    break;
                 case '5':
                    $info['type'] = "互助";
                    break;
                default:
                    $info['type'] = "签到";
               }
        }
        return $info;
    }


}