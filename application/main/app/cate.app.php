<?php
	/**
	 * Created by PhpStorm.
	 * User: dj
	 * Date: 2018/3/19
	 * Time: 14:51
	 */

    /*
     * @todo 攻略管理
     * @author dingj (2018年4月3日)
     */
	class cateApp extends BackendApp
    {
        public $mod;
        public $ic;
        public function __construct()
        {
            parent::__construct();
            $this->mod =m('cate');
            false && $this->mod = new CateMod();

            $this->ic =ic('cate');
            false && $this->ic  = new Cate();
        }

        /**
         * @todo    攻略列表
         * @author dingj (2018年4月3日)
         */
        public function ajax_list()
        {
            $cond[] ="parent_id != 0 AND mark=1";
            $search=trim($this->params['search']);
            if ($search)
                $cond[] = "`name` LIKE '%{$search}%'";

            $list = Zeus::pageData([
                        'cond' => $cond,
                        'order_by' => "id DESC"
                    ],
                        $this->mod,
                        'getCateInfo'
                    );
        $this->ajaxReturn($list);
    }

        /**
         * @todo    攻略的添加与编辑
         * @author dingj (2018年4月3日)
         */
        public function edit()
        {
            $id=$_REQUEST['id'];
            if (IS_POST) {
                $result=$this->ic->edit($_POST);
                $this->ajaxReturn($result);
            }
            //判断是编辑还是添加
            $info = [];
            if ($id) {
                $info = $this->mod->getInfo($id);
            }
            $this->assign('info', $info);
            $this->display();
        }

    }


