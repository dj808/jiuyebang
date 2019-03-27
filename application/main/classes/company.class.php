<?php
	
	/**
	 * 企业相关控制器
	 * Created by dingj
	 * Date: 2018/4/13  15:07
	 */
	class Company extends Zeus
    {
        public $mod;

        public function __construct()
        {
            $this->mod = m('company');
            false && $this->mod = new CompanyMod();

        }


        public function edit($param) {
            $id = (int)$param['id'];
            //获取一条记录
            $info = $this->mod->getInfo($id);

            $name = trim($param['name']);
            $existId = $this->mod->getRowByField("name", $name, $id);
            if ($existId)
                return R("该公司名称已存在", false);

            if (!$name)
                return R("请输入公司名称", false);

            $param['prov_id'] = $param['province_id'];
            $param['dist_id'] = $param['district_id'];

            if (!$param['logo'])
                return R("请上传企业logo", false);
           /* $address = trim($param['address']);
            if (!$address)
                return R("请输入详细地址", false);

            $legal_person = trim($param['legal_person']);
            if (!$legal_person)
                return R("请输入企业法人", false);


            $link_person = trim($param['link_person']);
            if (!$link_person)
                return R("请输入联系人", false);*/
            //格式化富文本
	        if($param['introduce'])
		        Zeus::saveFormatHtml($param['introduce']);

            //判断是否修改了审核状态
            $realStatus =  $param['status'];
            if($info['status'] != $realStatus){
                $title = '审核进度通知';
                if($realStatus ==3){    //如果是审核未通过
                    if(!$param['res_status'])
                        return R("请输入审核未通过的原因" , false);

                    $msg = ' 很遗憾，您发布的企业未通过审核，未通过原因为：'.$param['res_status'];
                }else{//如果是审核通过
                    $msg = ' 恭喜，您发布的培训通过审核！';
                }
            }

            $res = $this->mod->edit($param, $id);
            if (!$res)
                return R("添加失败", false);

            //判断是否修改了审核状态
            if($info['status'] != $realStatus){
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