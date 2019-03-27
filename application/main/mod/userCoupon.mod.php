<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/3/29
 * Time: 14:52
 */
class UserCouponMod extends CBaseMod
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct('user_coupon');
        $this->couponList=m('coupon')->getData([
            'cond'=>"mark=1 AND status=1",
            'order_by'=>"id DESC"
        ]);
    }
    /**
     * @todo    获取用户优惠券信息
     * @author dingj  (2018年05月15日)
     */
    public function getCouponInfo($id)
    {
        $info=parent::getInfo($id);

        $userInfo =$this->userMod->getInfo($info['user_id']);
        $info['user_id']=$userInfo['nickname'];

        $couponInfo =$this->couponMod->getInfo($info['coupon_id']);
        $info['coupon_id']=$couponInfo['name'];

        $info['add_time'] = $info['add_time'] ? date('Y-m-d H:i:s', $info['add_time']) : '';//添加时间
        $info['start_time'] = $info['start_time'] ? date('Y-m-d H:i:s', $info['start_time']) : '';
        $info['end_time'] = $info['end_time'] ? date('Y-m-d H:i:s', $info['end_time']) : '';
        $info['source_type'] = $info['source_type'] == 3 ? '活动送券' : ($info['source_type'] == 1 ? '后台发送' : '注册送券');//性别的判断
        $info['status'] = $info['status'] == 1 ? '已用' : '未用';

       return $info;
     }


}