<?php
	/**
	 * Created by PhpStorm.
	 * User: dj
	 * Date: 2018/3/19
	 * Time: 14:51
	 */

    /*
     * @todo 用戶管理
     * @author dingj (2018年3月17日)
     */
	class userApp extends BackendApp
    {
        public $mod;
        public $resume_mod;
        public $ic;

        public function __construct()
        {
            parent::__construct();
            $this->mod =m('user');
            false && $this->mod = new UserMod();

            $this->resume_mod =m('resume');
            false && $this->resume_mod = new ResumeMod();

            $this->ic = ic('user');
            false && $this->ic = new User();
        }
        /**
         * @todo   首页列表
         * @author dingj (2018年05月11日)
         */
        public function index () {
            //获取板块列表
            $realList = $this->mod->realList;
            $this->assign('realList',$realList);

            $companyList=$this->mod->companyList;
            $this->assign('companyList',$companyList);

            $typeList=$this->mod->typeList;
            $this->assign('typeList',$typeList);

            $statusList=$this->mod->statusList;
            $this->assign('statusList',$statusList);

            $couponList = $this->mod->getCouponList();
            $this->assign('couponList',$couponList);
            //用户的权限
            $user_role= session('userinfo');
            $this->assign('user_role',$user_role);

            parent::index();
        }

        /**
         * @todo    用戶列表
         * @author dingj (2018年3月17日)
         */
        public function ajax_list()
        {
            $cond[] ="mark=1";
            //按照昵称和手机查找
            $search=trim($this->params['search']);
            if ($search)
                $cond[] = "`nickname` LIKE '%{$search}%' OR
                      `mobile` LIKE '%{$search}%' ";
            //按认证状态查询
            if ($this->params['real_status'])
                $cond[] = "`real_status` = {$this->params['real_status']}";
            //按种子用户查询
            if ($this->params['is_seed'])
                $cond[] = "`is_seed` LIKE '%{$this->params['is_seed']}%'";
            //按用户性别查询
            if ($this->params['gender'])
                $cond[] = "`gender` LIKE '%{$this->params['gender']}%'";
            //按账户类型查询
            if ($this->params['type'])
                $cond[] = "`type` LIKE '%{$this->params['type']}%'";
            //按登录状态查询
            if ($this->params['status'])
                $cond[] = "`status` LIKE '%{$this->params['status']}%'";

            $list = Zeus::pageData([
                    'cond' => $cond,
                    'order_by' => "id DESC"
                 ],
                    $this->mod,
                    'getUserInfo'
                 );
            $this->ajaxReturn($list);
       }

        /**
         * @todo    用戶添加与编辑
         * @author dingj (2018年3月17日)
         */
        public function edit(){
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

            $this->assign("realList", $this->mod->realList);
            $this->assign('info', $info);

            $this->display();
        }

        /**
         * @todo    用戶详情
         * @author dingj (2018年3月18日)
         */
        public function detail()
        {
            $params = I('');
            //获取查询的一条用户记录
            $data = $this->mod->getInfo($params['id']);
            //查询用户的简历
            $resumeData = $this->resume_mod->getRowByField('user_id',$data['id']);

            //工作教育经历反序列化,获取详情
            $resumeData['education_exp']=unserialize($resumeData['education_exp']);

            $resumeData['job_exp']=unserialize($resumeData['job_exp']);

            //地址
            $resumeData['city_name']=m('city')->getCityName($resumeData['area_id']);
            $this->assign('info', $data);
            $this->assign('jobList', $resumeData['job_exp']);
            $this->assign('resumeinfo', $resumeData);
            $this->display();
        }

        
    }


