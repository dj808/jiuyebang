<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/3/26
 * Time: 11:05
 */
/*
 * 优惠券模型
 */
class couponMod extends CBaseMod{

    public function __construct()
    {
        parent::__construct('coupon');
        $this->typeList= [
            [
                'id'   => 1 ,
                'name' => '手动发放券'
            ] ,
            [
                'id'   => 2 ,
                'name' => '注册送券'
            ]
        ];
        $this->statusList= [
            [
                'id'   => 1 ,
                'name' => '启用'
            ] ,
            [
                'id'   => 2 ,
                'name' => '停用'
            ]
        ];
    }
    /**
     * @todo    获取优惠券信息
     * @author dingj  (2018年05月15日)
     */
    public function getCouponInfo($id)
    {
        $info=parent::getInfo($id);
        $info['add_time'] = $info['add_time'] ? date('Y-m-d H:i:s', $info['add_time']) : '';//添加时间
        $info['upd_time'] = $info['upd_time'] ? date('Y-m-d H:i:s', $info['upd_time']) : '';//更新时间
        $info['status'] = $info['status'] == 1 ? '启用' : '停用';
        $info['type'] = $info['type'] == 1 ? '手动发放券' : '注册送券';
        return $info;
    }


}