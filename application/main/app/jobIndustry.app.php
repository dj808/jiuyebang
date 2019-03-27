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
	class JobIndustryApp extends BackendApp
    {
        public $mod;

        public function __construct()
        {
            parent::__construct();
            $this->mod = m('JobIndustry');
            false && $this->mod = new JobIndustryMod();
        }

        /**
         * @todo    专业列表
         * @author dingj (2018年3月31日)
         */
        public function ajax_list()
        {
            $cond[] ="mark=1";
            $search=trim($this->params['search']);
            if ($search)
                $cond[] = "`name` LIKE '%{$search}%'";

        $list = Zeus::pageData([
                    'cond' => $cond,
                'order_by' => "id ASC"
                ],
                    $this->mod,
                    'getJobIndustryInfo'
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
                $data['name']=Zeus::filterFromData($name); //过滤表单注入
                $data=$params;
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
            $this->assign('info', $info);
            $this->display();
        }
        /**
         * @todo    删除数据
         * @author dingj  (2018年05月04日)
         */
        public function del() {
            $id = $this->params['id'];

            if(!$id)
                $this->ajaxReturn(R('参数丢失',false));

            $data = [
                'upd_user' => $this->uid,
                'upd_time' => time(),
                'mark' => -1,
            ];

            $res = $this->mod->edit($data, $id);

            if (!$res) {
                $this->ajaxReturn(R('9993'));
            }
            $this->ajaxReturn(R('9902', true));
        }

    }


