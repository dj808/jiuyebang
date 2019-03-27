<?php
	/**
	 * Created by PhpStorm.
	 * User: dj
	 * Date: 2018/3/19
	 * Time: 14:51
	 */

    /*
     * @todo 培训管理
     * @author dingj (2018年4月5日)
     */
	class trainingApp extends BackendApp
    {
        public $mod;
        public $ic;
        public function __construct()
        {
            parent::__construct();
            $this->mod = m('training');
            false && $this->mod = new TrainingMod();

            $this->ic = ic('training');
            false && $this->ic= new TrainingMod();
        }
        /**
         * @todo   首页列表
         * @author dingj (2018年06月13日)
         */
        public function index () {
            //获取板块列表
            $statusList = $this->mod->statusList;
            $this->assign('statusList',$statusList);
            parent::index();
        }
        /**
         * @todo    培训列表
         * @author dingj (2018年4月5日)
         */
        public function ajax_list()
        {
            $cond[] ="mark=1";
            $search=trim($this->params['search']);
            if ($search)
                $cond[] = "`title` LIKE '%{$search}%' OR
                      `tel` LIKE '%{$search}%' ";
            //根据状态
            if($this->params['status'])
                $cond[]= "`status` LIKE '%{$this->params['status']}%'";

            $list = Zeus::pageData([
                        'cond' => $cond,
                        'order_by' => "id DESC"
                    ],
                        $this->mod,
                        'getTrainingInfo'
                    );


            $this->ajaxReturn($list);
    }
        /**
         * @todo    培训添加与编辑
         * @author dingj (2018年4月5日)
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

            $train_industry_list=Zeus::config('train_industry_list');
            $train_level_list=Zeus::config('train_level_list');
            $train_cycle_list=Zeus::config('train_cycle_list');

            $this->assign('train_level_list', $train_level_list);
            $this->assign('train_industry_list', $train_industry_list);
            $this->assign('train_cycle_list', $train_cycle_list);
            $this->assign('statusList',$this->mod->statusList);
            $this->assign('info', $info);
            $this->display();
        }

        /**
         * @todo    培训详情
         * @author dingj (2018年4月5日)
         */
        public function detail()
        {
            $params = I('');
            //获取查询的一条用户记录
            $data = $this->mod->getInfo($params['id']);
            $this->assign('info', $data);
            $this->display();
        }
    }


