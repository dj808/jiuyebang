<?php
	/**
	 * Created by PhpStorm.
	 * User: dj
	 * Date: 2018/3/19
	 * Time: 14:51
	 */

    /*
     * @todo 收藏记录管理
     * @author dingj (2018年4月23日)
     */
	class collectApp extends BackendApp
    {
        public $mod;
        public $user_mod;
        public $job_mod;
        public function __construct()
        {
            parent::__construct();
            $this->mod =m('collect');
            false && $this->mod = new CollectMod();

            $this->user_mod =m('user');
            false && $this->user_mod = new UserMod();

        }
        /**
         * @todo   首页列表
         * @author Malcolm  (2018年04月12日)
         */
        public function index () {
            //获取板块列表
            $typeList = $this->mod->typeList;
            $this->assign('typeList',$typeList);
            parent::index();
        }

        /**
         * @todo    用戶职位申请列表
         * @author dingj (2018年3月31日)
         */
        public function ajax_list()
        {
            $cond[] ="mark=1";
            //根据用户昵称查找
            $search = trim($this->params['search']);
            if ($search){
                $tmp = "`nickname` LIKE '%{$search}%' AND mark = 1";
                $userIds = $this->user_mod->getIds($tmp);
                $userIds = implode(',' , $userIds);
                $cond[] = "`user_id` IN ({$userIds})";
            }
            //根据收藏类型查找
            if ($this->params['type'])
                $cond[] = "`type` LIKE '%{$this->params['type']}%'";

            $list = Zeus::pageData([
                    'cond' => $cond,
                    'order_by' => "id DESC"
                ],
                    $this->mod,
                    'getCollectInfo'
                );

        $this->ajaxReturn($list);
    }

        /**
         * @todo    用戶职位申请添加与编辑
         * @author dingj (2018年3月31日)
         */
        public function edit()
        {
            $id= (int) $_REQUEST['id'];
            $params=$this->params;
            if (IS_POST) {

                $data = [
                    'status' =>$params['status'],
                ];
                $res = $this->mod->edit($data, $id);
                if (!$res){
                    $this->ajaxReturn(R('9993'));
                }
                $this->ajaxReturn(R('9901', true));
            }
            //判断是编辑还是添加
            $info = [];
            if ($params['id']) {
                $info = $this->mod->getJobInfo($id);
            }
            $this->assign('status', $info['status_name']);
            $this->assign('info', $info);

            $this->display();
        }

    }


