<?php
	
	/**
	 * 优惠券相关控制器
	 * Created by dingj
	 * Date: 2018/4/13  15:07
	 */
	class Coupon extends Zeus
    {
        public $mod;
        public function __construct()
        {
            $this->mod = m('coupon');
            false && $this->mod = new CouponMod();

        }

        public function edit($post)
        {
            $id = (int) $post['id'];
            //过滤表单数据
            $data = $this->filterFromData($post);

            $name = trim($data['name']);
            $existId = $this->mod->getRowByField("name", $name, $id);
            if ($existId)
                return R("该名称已存在", false);

            if (!$name )
                return R('请输入名称', false);

            $type = (int)$data['type'];
            if (!$type)
                return R("请输入所属类型", false);

            $price = (int)$data['price'];
            if (!$price)
                return R("请输入面值", false);

            $duration = (int)$data['duration'];
            if (!$duration)
                return R("请输入有效天数", false);

            $data['status']=$data['status']== 'on' ? '1' : '2';

            $rs = $this->mod->edit($data, $id);
            if (!$rs) {
                return R("添加失败", false);
            }
            return R('操作成功', true);

        }


    }