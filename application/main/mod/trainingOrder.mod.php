<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/3/30
 * Time: 14:52
 */

/**
 * Class JobMod
 */
class trainingOrderMod extends CBaseMod
{

    /**
     * 构造函数
     */
    public function __construct()
    {

        parent::__construct('training_order');
        $this->trainingList=m('training')->getData([
            'cond'=>"mark=1",
            'order_by'=>"id DESC"
        ]);
        $this->statusList= [
            [
                'id'   => 1 ,
                'name' => '待支付'
            ] ,
            [
                'id'   => 2 ,
                'name' => '已支付'
            ] ,
            [
                'id'   => 3 ,
                'name' => '失败'
            ],
            [
                'id'   => 4 ,
                'name' => '已取消'
            ]
        ];
    }

    /**
     * @todo    获取培训申请信息
     * @author dingj  (2018年05月15日)
     */
    public function getOrderInfo($id)
    {
         $info=parent::getInfo($id);

         $info['pay_time'] = $info['pay_time'] ? date('Y-m-d H:i:s', $info['pay_time']) : '';//开始时间
         $info['add_time'] = $info['add_time'] ? date('Y-m-d H:i:s', $info['add_time']) : '';//结束时间
         //获取指定的课程
         $trainingInfo =$this->trainingMod->getInfo($info['training_id']);
         $info['training_id']=$trainingInfo['title'];
         //获取指定的用户
         $userInfo =$this->userMod->getInfo($info['user_id']);
         $info['user_id']=$userInfo['nickname'];

         $info['city_name']=$this->cityMod->getCityName($info['area_id']);
         $info['pay_type'] = $info['pay_type'] == 1 ? '支付宝' : ($info['real_status'] == 2 ? '微信' : '优惠券全额支付');//实名认证的判断
        //薪水类型
        switch ($info['pay_status']) {
            case '1':
                $info['pay_status'] = "待支付";
                break;
            case '2':
                $info['pay_status'] = "已支付";
                break;
            case '3':
                $info['pay_status'] = "失败";
                break;
            case '4':
                $info['pay_status'] = "已取消";
                break;
            default:
                $info['pay_status'] = "待支付";
        }
        return $info;
     }


}