<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/3/19
 * Time: 14:52
 */
class companyAdminMod extends CBaseMod
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct('company_admin');
        $this->typeList = [
            [
                'id'   => 1 ,
                'name' => '老板'
            ] ,
            [
                'id'   => 2 ,
                'name' => '人事总监/经理'
            ] ,
            [
                'id'   => 3 ,
                'name' => '部门总监/经理'
            ],
            [
                'id'   => 4 ,
                'name' => '人事专员'
            ]
        ];
    }
    /**
     * @todo    获取公司管理员信息
     * @author dingj  (2018年05月15日)
     */
    public function getListInfo($id)
    {
        $info = parent::getInfo($id);
                $info['add_time'] = $info['add_time'] ? date('Y-m-d H:i:s', $info['add_time']) : '';//添加时间
                $info['upd_time'] = $info['upd_time'] ? date('Y-m-d H:i:s', $info['upd_time']) : '';

                $companyInfo =$this->companyMod->getInfo($info['company_id']);
                $info['company_id']=$companyInfo['name'];

                $userInfo = $this->userMod->getInfo($info['user_id']);
                $info['user_id']=$userInfo['nickname'];
                //登录状态
                switch ($info['type']) {
                    case '1':
                        $info['type'] = "老板";
                        break;
                    case '2':
                        $info['type'] = "人事总监/经理";
                        break;
                    case '3':
                        $info['type'] = "部门总监/经理";
                        break;
                    case '4':
                        $info['type'] = "人事专员";
                        break;
                    default:
                        $info['type'] = "老板";
                }

            return $info;
     }


}