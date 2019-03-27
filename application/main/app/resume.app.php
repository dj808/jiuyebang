<?php
	/**
	 * Created by PhpStorm.
	 * User: dj
	 * Date: 2018/3/19
	 * Time: 14:51
	 */

    /*
     * @todo 用户简历管理
     * @author dingj (2018年4月9日)
     */
	class resumeApp extends BackendApp
    {
        public $mod;
        public $city_mod;
        public function __construct()
        {
            parent::__construct();
            $this->mod = m('resume');
            false && $this->mod = new UserMod();
            $this->city_mod =m('city');
            false && $this->city_mod = new CityMod();
        }

        /**
         * @todo    用户简历列表
         * @author dingj (2018年4月9日)
         */
        public function ajax_list()
        {
            $cond[] ="mark=1";
            $search=trim($this->params['search']);
            if ($search)
                $cond[] = "`name` LIKE '%{$search}%' OR
                      `tel` LIKE '%{$search}%' ";

        $list = Zeus::pageData([
                    'cond' => $cond,
                    'order_by' => "id DESC"
                ],
                    $this->mod,
                    'getResumeInfo'
                );

        $this->ajaxReturn($list);
    }

        /**
         * @todo    用户简历详情
         * @author dingj (2018年4月9日)
         */
        public function detail()
        {
            $params = I('');
            //获取查询的一条用户记录
            $data = $this->mod->getInfo($params['id']);

            $userInfo = m("user")->getInfo($data['user_id']);
            $data['user_id']=$userInfo['nickname'];
            //工作教育经历反序列化,获取详情
            $data['education_exp']=unserialize($data['education_exp']);

            $data['job_exp']=unserialize($data['job_exp']);
            //地址
            $data['city_name']=$this->city_mod->getCityName($data['area_id']);
            $this->assign('info', $data);
            $this->assign('jobList', $data['job_exp']);
            $this->display();
        }
    }


