<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/4/27
 * Time: 16:02
 */
    /*
     * @todo 互助申请管理
     * @author dingj (2018年4月27日)
     */
	class cooperationApplyApp extends BackendApp
    {
        public $mod;
        public $user_mod;
        public $cooperation_mod;
        public function __construct()
        {
            parent::__construct();
            $this->mod =m('cooperationApply');
            false && $this->mod = new cooperationApplyMod();

            $this->user_mod =m('user');
            false && $this->user_mod = new UserMod();

            $this->cooperation_mod =m('cooperation');
            false && $this->cooperation_mod = new CooperationMod();
        }

        /**
         * @todo    互助申请列表
         * @author dingj (2018年3月31日)
         */
        public function ajax_list()
        {
            $cond[] ="mark=1";
            $search = trim($this->params['search']);
            if ($search){
                $tmp = "`title` LIKE '%{$search}%'  AND mark = 1";
                $ids = $this->cooperation_mod->getIds($tmp);
                $ids = implode(',' , $ids );
                $cond[] = "`cooper_id` IN ({$ids})";
            }

            $list = Zeus::pageData([
                'cond' => $cond,
                'order_by' => "id DESC"
            ],
                $this->mod,
                'getApplyInfo'
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
                $info = $this->mod->getApplyInfo($id);
            }
            $this->assign('statusList', $this->mod->statusList);
            $this->assign('info', $info);

            $this->display();
        }

        /**
         * @todo    获取互助申请信息详情
         * @author dingj (2018年4月9日)
         */
        public function detail()
        {
            $params = I('');
            //获取查询的一条用户记录
            $data = $this->mod->getInfo($params['id']);
            $info=unserialize($data['snap']);
            //获取发送用户的信息
            $userInfo = m("user")->getInfo($info['user_id']);
            $info['username']=$userInfo['nickname'];
            //地址
            $info['city_name']=m('city')->getCityName($info['area_id']);

            $this->assign('info', $info);
            $this->display();
        }


    }


