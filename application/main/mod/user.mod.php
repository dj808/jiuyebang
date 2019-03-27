<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/3/19
 * Time: 14:52
 */
class UserMod extends CBaseMod
{
    /**
     * 构造函数
     */
    public function __construct(
    ){
        parent::__construct('user');
        $this->realList = [
            [
                'id'   => 1 ,
                'name' => '审核未通过'
            ] ,

            [
                'id'   => 2 ,
                'name' => '待审核'
            ] ,

            [
                'id'   => 3 ,
                'name' => '已审核'
            ],

        ];
        $this->statusList = [
            [
                'id'   => 1 ,
                'name' => '正常'
            ] ,
            [
                'id'   => 4 ,
                'name' => '已停用'
            ]
        ];
        $this->typeList = [
            [
                'id'   => 1 ,
                'name' => '普通用户'
            ] ,
            [
                'id'   => 2 ,
                'name' => '入住企业'
            ]
        ];
    }
    /**
     * @todo    获取用户信息
     * @author dingj  (2018年05月15日)
     */
    public function getUserInfo($id)
    {
        $info=parent::getInfo($id);
        $info['add_time'] = $info['add_time'] ? date('Y-m-d H:i:s', $info['add_time']) : '';//添加时间
        $info['gender'] = $info['gender'] == 3 ? '保密' : ($info['gender'] == 1 ? '男' : '女');//性别的判断
        $info['source'] = $info['source'] == 1 ? 'APP注册' : '后台添加';
        $info['type'] = $info['type'] == 1 ? '普通用户' : '入住企业';
        $info['is_seed'] = $info['is_seed'] == 1 ? '是' : '否';
        //是否有简历
        $tmp=$this->resumeMod->getRowByField('user_id', $info['id']);
        if($tmp)
            $info['is_resume']='有';
         else
            $info['is_resume']='无';

       //实名认证的判断
        switch ($info['real_status']) {
            case '1':
                $info['real_status'] = "审核未通过";
                break;
            case '2':
                $info['real_status'] = "待审核";
                break;
            case '3':
                $info['real_status'] = "已审核";
                break;
            default:
                $info['real_status'] = "待审核";
        }
        //登录状态
        switch ($info['status']) {
            case '1':
                $info['status'] = "正常";
                break;
            case '2':
                $info['status'] = "待审核";
                break;
            case '3':
                $info['status'] = "审核未通过";
                break;
            case '4':
                $info['status'] = "未通过";
                break;
            default:
                $info['status'] = "正常";
        }
            return $info;
     }
    /**
     * @todo    获取优惠券列表
     * @author Malcolm  (2018年05月15日)
     */
    public function getCouponList(){
        $cond[] = "type=1 AND status=1 AND mark = 1";

        $ids = $this->CouponMod->getIds($cond);
        $data = [];
        if ( is_array($ids) ) {
            foreach ( $ids as $key => $val ) {
                $info = $this->CouponMod->getInfo($val);
                $data[] = [
                    'id' => $info['id'],
                    'name' => $info['name'],
                ];
            }
        }

        return $data;
    }

}