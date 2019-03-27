<?php

class CompanyMod extends CBaseMod
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct('company');
        $this->typeList=[
             [
                 'id'=>1,
                 'name'=>'企业'
             ],
             [
                 'id'=>2,
                 'name'=>'培训机构'
             ]
        ];
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
     * @todo    获取企业信息
     * @author dingj  (2018年05月15日)
     */
    public function getListInfo($id)
    {
        $info=parent::getInfo($id);
        $info['add_time'] = $info['add_time'] ? date('Y-m-d H:i:s', $info['add_time']) : '';//开始时间
        $info['city_name']= $this->cityMod->getCityName($info['dist_id']);
        $info['status'] = $info['status'] == 1 ? '认证通过':($info['status'] == 2 ? '未认证':'认证未通过');
        $info['type'] = $info['type'] == 1 ? '企业':'培训机构';
        
        return $info;
    }

}
