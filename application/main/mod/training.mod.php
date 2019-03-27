<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/3/19
 * Time: 14:52
 */
class TrainingMod extends CBaseMod
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct('training');
        $this->statusList=[
            [
                'id'=>1,
                'name'=>'认证通过'
            ],
            [
                'id'=>2,
                'name'=>'未认证'
            ],
            [
                'id'=>3,
                'name'=>'认证未通过'
            ]
        ];
    }
    /**
     * @todo    获取培训信息
     * @author dingj  (2018年05月15日)
     */
    public function getTrainingInfo($id)
    {
        $info=parent::getInfo($id);
        $info['add_time'] = $info['add_time'] ? date('Y-m-d H:i:s', $info['add_time']) : '';//添加时间
        $info['upd_time'] = $info['upd_time'] ? date('Y-m-d H:i:s', $info['upd_time']) : '';//添加时间
        $info['is_recommend'] = $info['is_recommend'] == 1 ? '是' : '否';
        $info['is_coupon'] = $info['is_coupon'] == 1 ? '是' : '否';
        $info['status'] = $info['status'] == 1 ? '认证通过':($info['status'] == 2 ? '未认证':'认证未通过');
        $info['city_name'] =$this->cityMod->getCityName($info['area_id']);

        return $info;
     }


}