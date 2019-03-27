<?php
	/**
	 * Created by PhpStorm.
	 * User: dj
	 * Date: 2018/3/19
	 * Time: 14:51
	 */

    /*
     * @todo 用戶职位申请管理
     * @author dingj (2018年3月31日)
     */
	class jobApplyApp extends BackendApp
    {
        public $mod;
        public $user_mod;
        public $job_mod;
        public function __construct()
        {
            parent::__construct();
            $this->mod =m('jobApply');
            false && $this->mod = new JobApplyMod();

            $this->user_mod =m('user');
            false && $this->user_mod = new UserMod();

            $this->job_mod =m('job');
            false && $this->job_mod = new JobMod();
        }
        /**
         * @todo   首页列表
         * @author dingj (2018年05月3日)
         */
        public function index () {
            //获取职位类型
            $typeList = $this->mod->typeList;
            $this->assign('typeList',$typeList);
            //获取公司
            $companyList=$this->job_mod->getCompanyList();
            $this->assign('companyList',$companyList);
            //获取职位
            $jobList=$this->mod->getJobList();
            $this->assign('jobList',$jobList);
            parent::index();
        }

        /**
         * @todo    用戶职位申请列表
         * @author dingj (2018年3月31日)
         */
        public function ajax_list()
        {
            $cond[] ="mark=1";
            //获取查询的值
            $search=trim($this->params['search']);
            //用户名
            if ($search){
                $tmp = "`nickname` LIKE '%{$search}%' AND mark = 1";
                $userIds = $this->user_mod->getIds($tmp);
                $userIds = implode(',' , $userIds);
                $cond[] = "`user_id` IN ({$userIds})";
            }
            //按职位搜索
            if ($this->params['job_id'])
                $cond[] = "`job_id` = {$this->params['job_id']}";
            //按公司搜索
            if ($this->params['company_id'])
                $cond[] = "`company_id` LIKE '%{$this->params['company_id']}%'";

            $list = Zeus::pageData([
                    'cond' => $cond,
                    'order_by' => "id DESC"
                ],
                    $this->mod,
                    'getJobInfo'
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
            //判断是编辑还是添加
            $info = [];
            if ($params['id']) {
                $info = $this->mod->getJobInfo($id);
            }



            if (IS_POST) {
                $data = [
                    'status' =>$params['status'],
                    'check_time'=>time(),
                    'snap'=>unserialize($params['snap'])
                ];
                //判断是否修改了审核状态
                $status =$params['status'];
                if($info['status'] != $status){
                    $title = '审核进度通知';
                    if($status ==5){    //如果是审核未通过
                        if(!$params['res_status'])
                            return R("请输入审核未通过的原因" , false);

                        $msg = ' 很遗憾，您的职位申请未通过审核，未通过原因为：'.$params['res_status'];
                    }elseif($status ==1){
                        $msg = ' 恭喜，您的职位申请投递成功！';
                    }elseif($status ==2){
                        $msg = ' 恭喜，您的职位申请已查看！';
                    }elseif($status ==3){
                        $msg = ' 恭喜，您的职位申请待审核！';
                    }else{//如果是审核通过
                        $msg = ' 恭喜，您的职位申请已通过审核！';
                    }
                }

                $res = $this->mod->edit($data, $id);
                if (!$res){
                    $this->ajaxReturn(R('9993'));
                }
                //判断是否修改了审核状态
                if($info['status'] != $status){
                    Zeus::sendMsg([
                        'type' =>['msg','push'],
                        'user_id' =>$id,
                        'title' =>$title,
                        'content' =>$msg,
                        'msg_type' =>1,
                        'user_type' =>1,
                    ]);
                }
                $this->ajaxReturn(R('9901', true));
            }

            $this->assign('statusList', $this->mod->statusList);
            $this->assign('info', $info);
            $this->display();
        }
        /**
         * @todo    用戶申请详情
         * @author dingj (2018年6月19日)
         */
        public function detail()
        {
            $params = I('');
            //获取查询的一条用户记录
            $data = $this->mod->getInfo($params['id']);

            $info=$this->user_mod->getRowByField('id',$data['user_id']);

            $resumeData =m('resume')->getRowByField('user_id',$data['user_id']);
            //工作教育经历反序列化,获取详情
            $resumeData['education_exp']=unserialize($resumeData['education_exp']);

            $resumeData['job_exp']=unserialize($resumeData['job_exp']);

            //地址
            $resumeData['city_name']=m('city')->getCityName($resumeData['area_id']);

            $this->assign('info',$info);
            $this->assign('jobList', $resumeData['job_exp']);
            $this->assign('resumeinfo', $resumeData);
            $this->display();
        }
    }


