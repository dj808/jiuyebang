<?php
	/**
	 * Created by PhpStorm.
	 * User: dj
	 * Date: 2018/3/19
	 * Time: 14:51
	 */

    /*
     * @todo 用戶优惠券管理
     * @author dingj (2018年3月20日)
     */
	class userCouponApp extends BackendApp
    {
        public $mod;
        public $user_mod;
        public $coupon_mod;
        public function __construct()
        {
            parent::__construct();
            $this->mod =m('userCoupon');
            false && $this->mod = new userCouponMod();

            $this->user_mod =m('user');
            false && $this->user_mod = new UserMod();

            $this->coupon_mod =m('coupon');
            false && $this->coupon_mod = new CouponMod();
        }
        /**
         * @todo   首页列表
         * @author dingj (2018年05月3日)
         */
        public function index () {
            //获取板块列表
            $couponList=$this->mod->couponList;
            $this->assign('couponList',$couponList);
            parent::index();
        }
        /**
         * @todo    用戶优惠券列表
         * @author dingj (2018年3月20日)
         */
        public function ajax_list()
        {
             $cond[] = "mark=1";
            //按公司搜索
            if ($this->params['coupon_id'])
                $cond[] = "`coupon_id` LIKE '%{$this->params['coupon_id']}%'";
            //按用户昵称搜索
            $search = trim($this->params['search']);
            if ($search){
                $tmp = "`nickname` LIKE '%{$search}%' AND mark = 1";
                $userIds = $this->user_mod->getIds($tmp);
                $userIds = implode(',' , $userIds);
                $cond[] = "`user_id` IN ({$userIds})";
            }
            
                $list = Zeus::pageData([
                    'cond' => $cond,
                    'order_by' => "id DESC"
                ],
                    $this->mod,
                    'getCouponInfo'
                );

        $this->ajaxReturn($list);
    }

        /**
         * @todo    用戶优惠券详情
         * @author dingj (2018年3月20日)
         */
        public function detail()
        {
            $params = I('');
            //获取查询的一条用户记录
            $data = $this->mod->getInfo($params['id']);
            $this->assign('info', $data);
            $this->display();
        }
        /**
         * @todo    发放优惠券
         * @author dingj (2018年5月18日)
         */
        public function sendCoupon()
        {
            $data=[];
            $params = I('');
            //获取用户的id
            $user_id = $params['user_id'];
            if(!$user_id)
                return message('请选择相应的用户');
            //获取优惠券
            $coupon_id = $params['coupon_id'];
            if ($coupon_id)
                $info = $this->coupon_mod->getInfo($coupon_id);
            else
                return message('请选择相应的优惠券');

            foreach($user_id as $value){
                $data = [
                    'user_id'=>$value,
                    'coupon_id' => $coupon_id,
                    'status' => 2,
                    'source_type' => $info['type'],
                    'source_info' => '后台发送',
                    'use_time' => $info['duration'],
                    'coupon_price' => $info['price'],
                    'start_time' => time(),
                    'end_time' => time()+$info['duration']*24*3600,
                    'add_time'=>time()
                ];
            }

            $res = $this->mod->edit($data);

            if (!$res)
                $this->ajaxReturn(message('发送优惠券失败', false));
            else
                $this->ajaxReturn(message('发送券成功成功', true));

        }



    }


