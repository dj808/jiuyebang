<?php
	/**
	 * Created by PhpStorm.
	 * User: dj
	 * Date: 2018/3/19
	 * Time: 14:51
	 */

    /*
     * @todo 学校管理
     * @author dingj (2018年4月4日)
     */
	class collegeApp extends BackendApp
    {
        public $mod;
        public $ic;
        public function __construct()
        {
            parent::__construct();
            $this->mod = m('college');
            false && $this->mod = new CollegeMod();

            $this->ic = ic('college');
            false && $this->ic = new College();

        }

        /**
         * @todo    学校列表
         * @author dingj (2018年4月4日)
         */
        public function ajax_list()
        {
            //查询
            $cond[] ="mark=1";
            $name=trim($this->params['name']);
            if ($name)
                $cond[] = "`name` LIKE '%{$name}%' ";
            if ($this->params['grade'])
                $cond[] = "`grade` LIKE '%{$this->params['grade']}%'";

            $list = Zeus::pageData([
                        'cond' => $cond,
                        'order_by' => "id DESC"
                    ],
                        $this->mod,
                        'getCollegeInfo'
                    );
            $this->ajaxReturn($list);
        }

        /**
         * @todo    学校添加与编辑
         * @author dingj (2018年4月4日)
         */
        public function edit()
        {
            $id = (int) $_REQUEST['id'];

            if(IS_POST) {
                $result = $this->ic->edit($_POST, $id);
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

        public function export_excel(){

        }
    }


