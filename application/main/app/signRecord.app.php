<?php
	/**
	 * Created by PhpStorm.
	 * User: dj
	 * Date: 2018/3/19
	 * Time: 14:51
	 */

    /*
     * @todo 用户签到记录管理
     * @author dingj (2018年3月31日)
     */
	class signRecordApp extends BackendApp
    {
        public $mod;
        public $user_mod;

        public function __construct()
        {
            parent::__construct();
            $this->mod =m('signRecord');
            false && $this->mod = new signRecordMod();

            $this->user_mod =m('user');
            false && $this->user_mod = new UserMod();

        }

        /**
         * @todo    用戶职位申请列表
         * @author dingj (2018年3月31日)
         */
        public function ajax_list()
        {
            $cond[] ="mark=1";
            //获取用户和优惠券的一条记录
            $search = trim($this->params['search']);
            if ($search){
                $tmp = "`nickname` LIKE '%{$search}%' AND mark = 1";
                $ids = $this->user_mod->getIds($tmp);
                $ids = implode(',' , $ids);
                $cond[] = "`user_id` IN ({$ids})";
            }
            if($this->params['type'])
                $cond[] = "`type` LIKE '%{$this->params['type']}%'";

            $list = Zeus::pageData([
                    'cond' => $cond,
                    'order_by' => "id DESC"
                ],
                    $this->mod,
                    'getListInfo'
                );
        $this->ajaxReturn($list);
        }

   }


