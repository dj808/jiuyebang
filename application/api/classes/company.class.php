<?php

/**
 *  公司拓展控制器
 * Created by Malcolm.
 * Date: 2018/6/20  18:43
 */
class Company extends Zeus {
    public $mod;

    public function __construct() {
        parent::__construct("company");

        $this->mod = m('company');
        false && $this->mod = new CompanyMod();
    }


    /**
     * @todo    获取公司信息
     * @author  Malcolm  (2018年06月20日)
     */
    public function getCompanyInfo($param , $userId , $companyId) {
        $info = $this->mod->getInfo($companyId);

        $data = [
            'company_id'           => $companyId ,
            'company_name'         => $info['name'] ,
            'company_prov_id'      => $info['prov_id'] ,
            'company_city_id'      => $info['city_id'] ,
            'company_dist_id'      => $info['dist_id'] ,
            'company_address'      => $info['address'] ,
            'company_logo'         => $info['logo'] ,
            'company_tel'          => $info['tel'] ,
            'company_legal_person' => $info['legal_person'] ,
            'company_link_person'  => $info['link_person'] ,
            'company_link_phone'   => $info['link_phone'] ,
        ];


        return message('操作成功' , true , $data);
    }


    /**
     * @todo    修改公司信息
     * @author  Malcolm  (2018年06月20日)
     */
    public function editCompanyInfo($param , $userId , $companyId) {
        $provId = intval($param['prov_id']);
        $cityId = intval($param['city_id']);
        $distId = intval($param['dist_id']);
        $address = trim($param['address']);
        $tel = trim($param['tel']);
        $linkPerson = trim($param['link_person']);
        $linkPhone = trim($param['link_phone']);

        if (!$provId || !$cityId || !$distId)
            return message('请选择省市区');

        if (!$address)
            return message('请输入详细地址');

        if (!$tel)
            return message('请输入公司电话');

        if (!Zeus::isValidMobile($tel))
            return message('请输入正确的手机号');

        if (!$linkPerson)
            return message('请输入联系人');

        if (!$linkPhone)
            return message('请输入联系人手机号');

        if (!Zeus::isValidMobile($linkPhone))
            return message('请输入正确的手机号');

        $data = [
            'prov_id'     => $provId ,
            'city_id'     => $cityId ,
            'dist_id'     => $distId ,
            'address'     => $address ,
            'tel'         => $tel ,
            'link_person' => $linkPerson ,
            'link_phone'  => $linkPhone ,
        ];

        $rs = $this->mod->edit($data , $companyId);

        if (!$rs)
            return message('系统繁忙，请稍候再试');

        return message('操作成功' , true);
    }


    /**
     * @todo    获取我发布的职位
     * @author  Malcolm  (2018年06月20日)
     */
    public function geyMyJobList($param , $userId , $companyId) {
        $type = intval($param['type']);

        $cond = " type = {$type} AND is_task = 2 AND company_id = {$companyId} AND mark = 1 ";

        $page = $perpage = $limit = 0;
        $this->initPage($page , $perpage , $limit);

        $query = [
            'fields'   => 'id' ,
            'cond'     => $cond ,
            'limit'    => $limit ,
            'order_by' => ' id DESC ' ,
        ];

        $josMod = m('job');

        $list = $josMod->getData($query);
        $count = $josMod->getCount($query['cond']);

        $newList = [];


        if (is_array($list)) {
            foreach ($list as $key => $val) {
                $newList[] = $josMod->getListInfo($val['id']);
            }
        }

        $data = [
            'count'   => $count ,
            'page'    => $page ,
            'perpage' => $perpage ,
            'list'    => $newList
        ];

        return message('操作成功' , true , $data);
    }

    /**
     * @todo    获取我发布的培训
     * @author  Malcolm  (2018年06月20日)
     */
    public function getMyTrainList($param , $userId , $companyId) {
        $type = intval($param['type']);

        $cond = " company_id = {$companyId} AND mark = 1 ";

        $page = $perpage = $limit = 0;
        $this->initPage($page , $perpage , $limit);

        $query = [
            'fields'   => 'id' ,
            'cond'     => $cond ,
            'limit'    => $limit ,
            'order_by' => ' id DESC ' ,
        ];

        $trainMod = m('training');

        $list = $trainMod->getData($query);
        $count = $trainMod->getCount($query['cond']);

        $newList = [];


        if (is_array($list)) {
            foreach ($list as $key => $val) {
                $newList[] = $trainMod->getListInfo($val['id']);
            }
        }

        $data = [
            'count'   => $count ,
            'page'    => $page ,
            'perpage' => $perpage ,
            'list'    => $newList
        ];

        return message('操作成功' , true , $data);
    }


    /**
     * @todo    修改职位状态
     * @author  Malcolm  (2018年06月20日)
     */
    public function setJobStatus($param , $userId , $companyId) {
        $jobId = intval($param['job_id']);

        $status = intval($param['status']);

        if (!$jobId || !$status)
            return message('参数丢失');

        if ($status != 1 && $status != 2 && $status != 3)
            return message('参数错误');

        $data['status'] = $status;

        $jobMod = m('job');

        $info = $jobMod->getInfo($jobId);
        if ($info['company_id'] != $companyId)
            return message('仅能修改自己发布的职位');


        //查询状态
        if ($info['auth_status'] != 1 && $status == 2)
            return message('仅审核通过的职位可以下架');

        if ($status < 3)
            $rs = $jobMod->edit($data , $jobId);
        else
            $rs = $jobMod->drop($jobId);

        if (!$rs)
            return message('系统繁忙，请稍候再试');

        return message('操作成功' , true);
    }

    /**
     * @todo    修改培训状态
     * @author  Malcolm  (2018年06月20日)
     */
    public function setTrainStatus($param , $userId , $companyId) {
        $trainId = intval($param['train_id']);

        $status = intval($param['status']);

        if (!$trainId || !$status)
            return message('参数丢失');

        if ($status != 1 && $status != 2 && $status != 3)
            return message('参数错误');

        $data['status'] = $status;
        if($status==2)
            $data['status'] = 4;

        $trainMod = m('training');

        $info = $trainMod->getInfo($trainId);
        if ($info['company_id'] != $companyId)
            return message('仅能修改自己发布的职位');


        //查询状态
        if ($info['status'] != 1 && $status == 2)
            return message('仅审核通过的培训可以下架');

        if ($status < 3)
            $rs = $trainMod->edit($data , $trainId);
        else
            $rs = $trainMod->drop($trainId);

        if (!$rs)
            return message('系统繁忙，请稍候再试');

        return message('操作成功' , true);
    }


}