<?php
	/**
	 * Created by PhpStorm.
	 * User: dj
	 * Date: 2018/3/19
	 * Time: 14:51
	 */

    /*
     * @todo 培训报名管理
     * @author dingj (2018年4月10日)
     */
	class trainingOrderApp extends BackendApp
    {
        public $mod;
        public $training_mod;
        public $user_mod;
        public $coupon_mod;
        public $usercoupon_mod;

        public function __construct()
        {
            parent::__construct();
            $this->mod =m('trainingOrder');
            false && $this->mod = new trainingOrderMod();

            $this->training_mod =m('training');
            false && $this->training_mod = new TrainingMod();

            $this->user_mod =m('user');
            false && $this->user_mod = new UserMod();

            $this->usercoupon_mod =m('coupon');
            false && $this->usercoupon_mod = new usercouponMod();
        }
        /**
         * @todo   首页列表
         * @author dingj (2018年05月3日)
         */
        public function index () {
            //获取板块列表
            $trainingList = $this->mod->trainingList;
            $this->assign('trainingList',$trainingList);

            $statusList = $this->mod->statusList;
            $this->assign('statusList',$statusList);
            parent::index();
        }
        /**
         * @todo    培训报名列表
         * @author dingj (2018年4月10日)
         */
        public function ajax_list()
        {
            $cond[] ="mark=1";

            //按培训课程搜索
            if ($this->params['training_id'])
                $cond[] = "`training_id` LIKE '%{$this->params['training_id']}%'";
            //按用户昵称搜索
            $search = trim($this->params['search']);
            if ($search){
                $tmp = "`nickname` LIKE '%{$search}%' AND mark = 1";
                $userIds = $this->user_mod->getIds($tmp);
                $userIds = implode(',' , $userIds);
                $cond[] = "`user_id` IN ({$userIds})";
            }
            //按支付状态搜索
            if ($this->params['pay_status'])
                $cond[] = "`pay_status` LIKE '%{$this->params['pay_status']}%'";


           $list = Zeus::pageData([
                    'cond' => $cond,
                    'order_by' => "id DESC"
                ],
                    $this->mod,
                    'getOrderInfo'
                );
                $this->ajaxReturn($list);
    }


        /**
         * @todo    培训报名详情
         * @author dingj (2018年4月11日)
         */
        public function detail()
        {
            $params = I('');
            //获取查询的一条用户记录
            $data = $this->mod->getInfo($params['id']);
            //获取订单用户
            $userData=$this->user_mod->getInfo($data['user_id']);
            $data['user_id']=$userData['nickname'];
           //获取培训课程
            $trainingData=$this->training_mod->getInfo($data['training_id']);
            $data['training_id']=$trainingData['title'];

            $this->assign('info', $data);
            $this->display();
        }
    }


