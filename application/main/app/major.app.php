<?php
	/**
	 * Created by PhpStorm.
	 * User: dj
	 * Date: 2018/3/19
	 * Time: 14:51
	 */

    /*
     * @todo 专业管理
     * @author dingj (2018年3月20日)
     */
	class majorApp extends BackendApp
    {
        public $mod;

        public function __construct()
        {
            parent::__construct();
            $this->mod = m('major');
            false && $this->mod = new MajorMod();
        }

        /**
         * @todo    专业列表
         * @author dingj (2018年3月31日)
         */
        public function ajax_list()
        {
            $cond[] ="mark=1 AND pid!=0";
            $search=trim($this->params['search']);
            if ($search)
                $cond[] = "`name` LIKE '%{$search}%'";

        $list = Zeus::pageData([
                    'cond' => $cond,
                    'order_by' => "id DESC"
                ],
                    $this->mod,
                    'getMajorInfo'
                );
        $this->ajaxReturn($list);
    }

        /**
         * @todo    专业添加与编辑
         * @author dingj (2018年3月31日)
         */
        public function edit()
        {
            $params=$this->params;

            if (IS_POST) {
                $name=trim($params['name']);
                $existId = $this->mod->getRowByField("name", $name, $params['id']);
                if ($existId)
                    return R("该名称已存在", false);
                if (!$name )
                    return R('请输入名称', false);

                $data['name']=Zeus::filterFromData($name); //过滤表单注入
                $data['pid']=trim($params['pid']);
                $res = $this->mod->edit($data, $params['id']);
                if (!$res) {
                    $this->ajaxReturn(R('9903' , false));
                 }
                $this->ajaxReturn(R('9901' , true));
            }
            //判断是编辑还是添加
            $info = [];
            if ($params['id']) {
                $info = $this->mod->getInfo($params['id']);
            }
            $query = [
                'cond' => 'mark=1 AND pid=0',
            ];
            $top_menu=$this->mod->getData($query);
            $this->assign('top_menu', $top_menu);
            $this->assign('info', $info);
            $this->display();
        }

    }


