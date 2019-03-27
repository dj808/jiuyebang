<?php
	
	/**
	 * 用户相关控制器
	 * Created by dingj
	 * Date: 2018/4/13  15:07
	 */
	class Fun extends Zeus
    {
        public $mod;

        public function __construct()
        {
            $this->mod = m('fun');
            false && $this->mod = new FunMod();

        }


        public function edit($param)
        {
            $id = (int) $param['id'];

            $info = $this->mod->getInfo($id);
            //判断是否修改了审核状态
            $status =$param['status'];
            if($info['status'] != $status){
                $title = '审核进度通知';
                if($status ==3){    //如果是审核未通过
                    if(!$param['res_status'])
                        return R("请输入审核未通过的原因" , false);
                    $msg = ' 很遗憾，您的趣事未通过审核，未通过原因为：'.$param['res_status'];
                }else{//如果是审核通过
                    $msg = ' 恭喜，您的趣事已通过审核！';
                }
            }

            $rs = $this->mod->edit($param, $id);
            if (!$rs) {
                return R("添加失败", false);
            }
            //自动维护经纬度
            Zeus::push('gpsManage', ['type' => 'fun', 'typeId' => $id]);
            //判断是否修改了审核状态
            if($info['status'] != $status){
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