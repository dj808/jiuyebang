<?php
	
	/**
	 * 兼职任务相关控制器
	 * Created by dingj
	 * Date: 2018/4/13  15:07
	 */
	class Jobtask extends Zeus
    {
        public $mod;

        public function __construct()
        {
            $this->mod = m('job');
            false && $this->mod = new JobMod();

        }


        public function edit($param)
        {
            $id = (int) $param['id'];
            $title = trim($param['title']);
            $existId = $this->mod->getRowByField("title", $title, $id);
            if ($existId)
                return R("该职位名称已存在", false);

            if (!$title)
                return R("请输入职位名称", false);

            $company_id = (int)$param['company_id'];
            if (!$company_id)
                return R("请输入公司名称", false);

            if (!$param['money_type'])
                return R("请输入薪水类型", false);


            $language= trim($param['language']);
            if (!$language)
                return R("请输入语言要求", false);

            $num_lower = (int)$param['num_lower'];
            $num_upper = (int)$param['num_upper'];
            if (!$num_lower && !$num_upper) {
                return R('请选择人数范围', false);
            }

            $money= (int)$param['money_upper'];
            if (!$money) {
                return R('请选择兼职薪水', false);
            }

            if (!$param['education'])
                return R("请输入学历要求", false);

            if (!$param['experience'])
                return R("请输入经验要求", false);

            if($param['job_type_ids'])
                $param['job_type_ids'] = implode(',',$param['job_type_ids']);

            if($param['tag_ids'])
                $param['tag_ids'] = implode(',',$param['tag_ids']);

            $address = trim($param['address']);
            if (!$address)
                return R("请输入详细地址", false);

            $link_person= trim($param['link_person']);
            if (!$link_person)
                return R("请输入联系人", false);

           /* $link_phone = $this->isValidMobile($param['link_phone']);
            if (!$link_phone)
                return R("请输入正确格式的手机号码", false);*/

            $param['type']=2;
            $param['prov_id']=$param['province_id'];
            $param['dist_id']=$param['district_id'];
            $param['money_lower｜money_upper']=$money;

            $res = $this->mod->edit($param, $id);
            if (!$res) {
                return R("添加失败", false);
            }

            if (!$this->$id)
                $id = $res;
            else
                $id = $this->$id;

            //自动维护经纬度
            Zeus::push('gpsManage', ['type' => 'job', 'typeId' => $id]);

            return R('操作成功', true);

        }

    }