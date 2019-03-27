<?php
	/**
	 * Created by PhpStorm.
	 * User: dj
	 * Date: 2018/3/19
	 * Time: 14:51
	 */

    /*
     * @todo 企业管理员管理
     * @author dingj (2018年4月12日)
     */
	class companyAdminApp extends BackendApp
    {
        public $mod;
        public $company_mod;
        public $user_mod;


        public function __construct()
        {
            parent::__construct();
            $this->mod = m('companyAdmin');
            false && $this->mod = new companyAdminMod();

            $this->company_mod =m('company');
            false && $this->company_mod = new CompanyMod();

            $this->user_mod =m('user');
            false && $this->user_mod = new UserMod();
        }
        /**
         * @todo   首页列表
         * @author dingj (2018年05月3日)
         */
        public function index () {

            $companyList = m('job')->getCompanyList();
            $this->assign('companyList',$companyList);

            //获取板块列表
            $typeList = $this->mod->typeList;
            $this->assign('typeList',$typeList);
            parent::index();
        }
        /**
         * @todo    企业管理员列表
         * @author dingj (2018年4月12日)
         */
        public function ajax_list()
        {
            $cond[] ="mark=1";

            if ($this->params['search'])
                $cond[] = "`nickname` LIKE '%{$this->params['search']}%' ";
            //按公司名称搜索
            if ($this->params['company_id'])
                $cond[] = "`company_id` LIKE '%{$this->params['company_id']}%'";
            //按用户类型名称搜索
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
         * @todo    企业管理员添加与编辑
         * @author dingj (2018年4月12日)
         */
        public function edit()
        {
            $params=$this->params;
            if (IS_POST) {
                $data = [
                    'company_id' =>$params['company_id'],
                    'user_id' => $params['user_id'],
                    'type' => $params['type'],
                    'type_show' => $params['type_show']
                ];
                $res = $this->mod->edit($data, $params['id']);
                if (!$res){
                    $this->ajaxReturn(R('9993'));
                }
                $this->ajaxReturn(R('9901', true));
                }
            //判断是编辑还是添加
            $info = [];
            if ($params['id']) {
                $info = $this->mod->getInfo($params['id']);
            }

            $companyList=Zeus::pageData(['mark' => 1], $this->company_mod, 'getShortInfo');
            $userList=Zeus::pageData(['mark' => 1], $this->user_mod, 'getShortInfo');

            $this->assign('companyList',$companyList['data']);
            $this->assign('userList',$userList['data']);
            $this->assign('info', $info);
            $this->display();
        }

    }


