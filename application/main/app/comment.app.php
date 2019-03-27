<?php
	/**
	 * Created by PhpStorm.
	 * User: dj
	 * Date: 2018/3/19
	 * Time: 14:51
	 */

    /*
     * @todo 评论管理
     * @author dingj (2018年4月26日)
     */
	class commentApp extends BackendApp
    {
        public $mod;
        public $user_mod;

        public function __construct()
        {
            parent::__construct();
            $this->mod =m('comment');
            false && $this->mod = new CommentMod();

            $this->user_mod =m('user');
            false && $this->user_mod = new UserMod();
        }
        /**
         * @todo   首页列表
         * @author dingj (2018年05月3日)
         */
        public function index () {
            //获取板块列表
            $typeList = $this->mod->typeList;
            $this->assign('typeList',$typeList);
            parent::index();
        }
        /**
         * @todo    评论列表
         * @author dingj (2018年4月10日)
         */
        public function ajax_list()
        {
            $cond[] ="mark=1";
            //按用户昵称搜索
            $search = trim($this->params['search']);
            if ($search){
                $tmp = " `nickname` LIKE '%{$search}%' AND mark = 1";
                $ids = $this->user_mod->getIds($tmp);
                $ids = implode(',' , $ids);
                $cond[] = "`user_id` IN ({$ids})";
            }
            //按类型搜索
            if ($this->params['type'])
                $cond[] = "`type` LIKE '%{$this->params['type']}%'";

           $list = Zeus::pageData([
                    'cond' => $cond,
                    'order_by' => "id DESC"
                ],
                    $this->mod,
                    'getCommentInfo'
                );

        $this->ajaxReturn($list);
    }

        /**
         * @todo    评论审核
         * @author dingj (2018年4月26日)
         */
        public function edit()
        {
            $id= (int) $_REQUEST['id'];
            $params=$this->params;
            if (IS_POST) {
                $data = [
                    'state' =>$params['state'],
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
                $info = $this->mod->getCommentInfo($id);
            }
            $this->assign('state_name', $info['state_name']);
            $this->assign('info', $info);

            $this->display();
        }

    }


