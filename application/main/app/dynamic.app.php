<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/4/27
 * Time: 14:34
 */

/**
 * 校园新鲜事控制器
 * User: dingj
 * Date: 2018/4/27
 * Time: 14:51
 */
class dynamicApp extends BackendApp {
    public $mod;
    public $user_mod;
    public $ic;

    public function __construct () {
        parent::__construct();
        $this->mod =m('dynamic');
        false && $this->mod = new DynamicMod();

        $this->user_mod =m('user');
        false && $this->user_mod = new UserMod();

        $this->ic = ic('dynamic');
        false && $this->ic = new Dynamic();
    }

    /**
     * @todo    校园新鲜事列表
     * @author  dingj  (2018年04月27日)
     */
    public function ajax_list () {
        $cond[] = "mark=1";
        $search = trim($this->params['search']);

        if ($search) {
            $tmp = "`nickname` LIKE '%{$search}%'";
            $ids = $this->user_mod->getIds($tmp);

            $ids = implode(',' , $ids);
            $cond[] = "`user_id` IN ({$ids})";
        }
        $data = Zeus::pageData([
            'cond'     => $cond ,
            'order_by' => "id DESC"
        ] ,
            $this->mod,
            'getListInfo'
        );

        $this->ajaxReturn($data);
    }

    /**
     * @todo   校园新鲜事添加与编辑
     * @author  dingj (2018年04月27日)
     */
    public function detail () {
        $id = (int) $_REQUEST['id'];
        //判断是编辑还是添加
        $info = $this->mod->getInfo($id);
        //获取制定的用户
        $userinfo=$this->user_mod->getInfo($info['user_id']);
        $info['username']=$userinfo['nickname'];
        $imgsList=unserialize($info['imgs']);

        $this->assign('info', $info);
        $this->assign('imgsList', $imgsList);
        $this->display();
    }

}


