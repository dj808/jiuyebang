<?php
	
	/**
	 * 评论相关控制器
	 * Created by dingj
	 * Date: 2018/4/13  15:07
	 */
	class Comment extends Zeus
    {
        public $mod;

        public function __construct()
        {
            $this->mod = m('comment');
            false && $this->mod = new CommentMod();

        }

        public function edit($param)
        {
            $id = (int) $param['id'];
            //获取评论的信息
            $info = $this->mod->getInfo($id);
            $state = (int)$param['state'];
            if (!$state)
                return R("请输入审核状态", false);

            if($info['state'] != $state){
                $title = '审核进度通知';
                if($state ==2){    //如果是审核未通过
                    if(!$param['res_status'])
                        return R("请输入审核未通过的原因" , false);

                    $msg = ' 很遗憾，您的评论未通过审核，未通过原因为：'.$param['res_status'];
                }else{//如果是审核通过
                    $msg = ' 恭喜，您的评论已通过审核！';
                }
            }

            $rs = $this->mod->edit($param, $id);
            if (!$rs) {
                return R("添加失败", false);
            }
            //判断是否修改了审核状态
            if($info['state'] != $state){
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