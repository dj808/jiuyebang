<?php
	
	/**
	 * 培训相关控制器
	 * Created by dingj
	 * Date: 2018/4/13  15:07
	 */
	class Training extends Zeus
    {
        public $mod;
        public function __construct()
        {
            $this->mod = m('training');
            false && $this->mod = new TrainingMod();

        }

        public function edit($param)
        {
            $id = (int) $param['id'];
            //获取一条记录
            $info = $this->mod->getInfo($id);

            $param['area_id']= (int)$param['district_id'];
            $title =trim($param['title']);
            $existId = $this->mod->getRowByField("title", $title, $id);
            if ($existId)
                return R("培训课程已存在", false);

            if (!$title) {
                return R("请输入标题名称", false);
            }

            $price = (float)$param['price'];
            if (!$price)
                return R('请输入价格', false);

            if (!$param['address']) {
                return R("请输入详细地址", false);
            }

           /* $tel = $this->isValidMobile($param['tel']);
            if (!$tel) {
                return R("请输入正确格式的手机号码", false);
            }*/

            if (!$param['cover']) {
                return R("请输入封面图", false);
            }
            if (!$param['top_img']) {
                return R("请输入顶部图片", false);
            }
            $is_coupon = (int)$param['is_coupon'];
            if (!$is_coupon)
                return R('请输入是否使用优惠券', false);

            $is_recommend = (int)$param['is_recommend'];
            if (!$is_recommend)
                return R('请输入是否推荐', false);

            $class_num = (int)$param['class_num'];
            if (!$class_num)
                return R('请输入课程数', false);

            $class_duration = (int)$param['class_duration'];
            if (!$class_duration)
                return R('请输入课程时长', false);

            if (!$param['class_date'])
                return R('请输入上课时间', false);

            //格式化富文本
            if($param['class_intro'])
                Zeus::saveFormatHtml($param['class_intro']);

            //判断是否修改了审核状态
            $realStatus =$param['status'];
            if($info['status'] !=$realStatus){
                $title = '审核进度通知';
                if($realStatus ==3){    //如果是审核未通过
                    if(!$param['res_status'])
                        return R("请输入审核未通过的原因" , false);

                    $msg = ' 很遗憾，您发布的培训未通过审核，未通过原因为：'.$param['res_status'];
                }else{//如果是审核通过
                    $msg = ' 恭喜，您发布的培训通过审核！';
                }
            }

            $rs = $this->mod->edit($param, $id);
            if (!$rs)
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