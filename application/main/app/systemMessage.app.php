<?php
	/**
	 * Created by PhpStorm.
	 * User: dj
	 * Date: 2018/3/19
	 * Time: 14:51
	 */

    /*
     * @todo 系统消息管理
     * @author dingj (2018年4月31日)
     */
	class systemMessageApp extends BackendApp
    {
        public $mod;
        public $user_mod;

        public function __construct()
        {
            parent::__construct();
            $this->mod =m('systemMessage');
            false && $this->mod = new systemMessageMod();

            $this->user_mod =m('user');
            false && $this->user_mod = new UserMod();

        }

        /**
         * @todo    系统消息列表
         * @author dingj (2018年3月31日)
         */
        public function ajax_list()
        {
            $cond[] ="mark=1";
            //获取用户和优惠券的一条记录
            $search = trim($this->params['search']);
            if ($search){
                $tmp = " `nickname` LIKE '%{$search}%' AND mark = 1";
                $ids = $this->user_mod->getIds($tmp);
                $ids = implode(',' , $ids);
                $cond[] = "`user_id` IN ({$ids})";
            }
            if($this->params['msg_type'])
                $cond[] = "`msg_type` LIKE '%{$this->params['msg_type']}%'";

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


