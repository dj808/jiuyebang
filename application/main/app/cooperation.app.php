<?php
	/**
	 * Created by PhpStorm.
	 * User: dj
	 * Date: 2018/3/19
	 * Time: 14:51
	 */

    /*
     * @todo 互助管理
     * @author dingj (2018年3月30日)
     */
	class cooperationApp extends BackendApp
    {
        public $mod;
        public $user_mod;
        public $ic;

        public function __construct()
        {
            parent::__construct();
            $this->mod =m('cooperation');
            false && $this->mod = new CooperationMod();

            $this->user_mod =m('user');
            false && $this->user_mod = new UserMod();

            $this->ic =ic('cooperation');
            false && $this->ic = new Cooperation();

        }

        /**
         * @todo    互助列表
         * @author dingj (2018年4月23日)
         */
        public function ajax_list()
        {
            //根据条件搜索
            $cond[] ="mark=1";
            $search=trim($this->params['search']);
            if($search){
                $tmp="mark=1 AND `nickname` LIKE '%{$search}%'";
                $ids=$this->user_mod->getIds($tmp);
                $ids=implode(',',$ids);
                $cond[]=" (`title` LIKE '%{$search}%' OR `user_id` IN ({$ids}))";
            }

            if ($this->params['type'])
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

        /**
         * @todo    兼职职位添加与编辑
         * @author dingj (2018年3月30日)
         */
        public function edit()
        {

            $id=$_REQUEST['id'];
            if (IS_POST) {
                $result=$this->ic->edit($_POST, $id);
                $this->ajaxReturn($result);
            }
            //判断是编辑还是添加
            $info = [];
            if ($id) {
                $info = $this->mod->getInfo($id);
            }
            //获取所有的用户
            $userInfo = $this->user_mod->getInfo($info['user_id']);
            $info['username']=$userInfo['nickname'];

            $this->assign('info', $info);
            $this->display();
        }

    }


