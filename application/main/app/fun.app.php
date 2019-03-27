<?php
/**
 * 趣事控制器
 * User: dingj
 * Date: 2018/4/27
 * Time: 14:51
 */
class funApp extends BackendApp {
    public $mod;
    public $user_mod;
    public $ic;

    public function __construct () {
        parent::__construct();
        $this->mod =m('fun');
        false && $this->mod = new FunMod();

        $this->user_mod =m('user');
        false && $this->user_mod = new UserMod();

        $this->ic = ic('fun');
        false && $this->ic = new Fun();
    }
    /**
     * @todo   首页列表
     * @author dingj (2018年05月3日)
     */
    public function index () {
        //获取板块列表
        $statusList = $this->mod->statusList;
        $this->assign('typeList',$statusList);

        parent::index();
    }
    /**
     * @todo    趣事列表
     * @author  dingj  (2018年04月27日)
     */
    public function ajax_list () {
        $cond[] = "mark=1";
        $search = trim($this->params['search']);

        if ($search) {
            $tmp = " `nickname` LIKE '%{$search}%'";
            $ids = $this->user_mod->getIds($tmp);

            $ids = implode(',' , $ids);
            $cond[] = " (`title` LIKE '%{$search}%' OR  `user_id` IN ({$ids}))";
        }
        $data = Zeus::pageData([
            'cond'     => $cond ,
            'order_by' => "id DESC"
        ] ,
            $this->mod ,
            'getFunInfo'
        );

        $this->ajaxReturn($data);
    }

    /**
     * @todo   趣事添加与编辑
     * @author  dingj (2018年04月27日)
     */
    public function edit () {
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
        $userdata=$this->user_mod->getInfo($info['user_id']);
        $info['username']=$userdata['nickname'];
        $this->assign('statusList', $this->mod->statusList);
        $this->assign('info', $info);

        $this->display();
     }

}


