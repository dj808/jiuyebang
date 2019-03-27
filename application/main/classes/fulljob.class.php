<?php
	
	/**
	 * 全职相关控制器
	 * Created by dingj
	 * Date: 2018/4/13  15:07
	 */
	class Fulljob extends Zeus
    {
        public $mod;

        public function __construct()
        {
            $this->mod = m('job');
            false && $this->mod = new JobMod();

        }


        public function edit($param) {
            $id = (int)$param['id'];
            $info = $this->mod->getInfo($id);
            //标题
            $title = trim($param['title']);
            $existId = $this->mod->getRowByField("title", $title, $id);
            if ($existId)
                return R("该职位名称已存在", false);

            if (!$title)
                return R("请输入职位名称", false);

            $company_id = (int)$param['company_id'];
            if (!$company_id)
                return R("请输入公司名称", false);

            $num_lower = (int)$param['num_lower'];
            $num_upper = (int)$param['num_upper'];
            if (!$num_lower && !$num_upper) {
                return R('请选择人数范围', false);
            }

            $money_lower = (int)$param['money_lower'];
            $money_upper = (int)$param['money_upper'];
            if (!$money_lower && !$money_upper) {
                return R('请选择薪水范围', false);
            }

            if (!$param['industry_id'])
                return R("请输入所属行业", false);

            if (!$param['education'])
                return R("请输入学历要求", false);

            if (!$param['experience'])
                return R("请输入经验要求", false);
	
	
	        if($param['job_type_ids'])
		        $param['job_type_ids'] = implode(',',$param['job_type_ids']);

	        if($param['tag_ids'])
		        $param['tag_ids'] = implode(',',$param['tag_ids']);
		
		
	        if (!$param['job_type_ids'])
                return R("请输入职位类型", false);

            if (!$param['tag_ids'])
                return R("请输入职位标签", false);

            /*if (!$param['district_id'])
                return R("请输入省市区", false);*/

           /* $address = trim($param['address']);
            if (!$address)
                return R("请输入详细地址", false);

            $language = trim($param['language']);
            if (!$language)
                return R("请输入语言要求", false);*/

            $link_person = trim($param['link_person']);
            if (!$link_person)
                return R("请输入联系人", false);

            /*$link_phone = $this->isValidMobile($param['link_phone']);
            if (!$link_phone)
                return R("请输入正确格式的手机号码", false);*/
            $param['type'] = 1;
            $param['prov_id'] = $param['province_id'];
            $param['dist_id'] = $param['district_id'];
            //格式化富文本
	        if($param['content'])
		        Zeus::saveFormatHtml($param['content']);
            //判断是否修改了审核状态
            $realStatus =  $param['auth_status'];
            if($info['auth_status'] != $realStatus){
                $title = '审核进度通知';
                if($realStatus ==3){    //如果是审核未通过
                    if(!$param['res_status'])
                        return R("请输入审核未通过的原因" , false);

                    $msg = ' 很遗憾，您的职位申请未通过审核，未通过原因为：'.$param['res_status'];
                }else{//如果是审核通过
                    $msg = ' 恭喜，您的职位申请已通过审核！';
                }
            }


            $res = $this->mod->edit($param, $id);
            if (!$res) {
                return R("添加失败", false);
            }
            //判断是否修改了审核状态
            if($info['auth_status'] != $realStatus){
                Zeus::sendMsg([
                    'type' =>['msg','push'],
                    'user_id' =>$id,
                    'title' =>$title,
                    'content' =>$msg,
                    'msg_type' =>1,
                    'user_type' =>1,
                ]);
            }

            return R('操作成功', true);

        }
    }