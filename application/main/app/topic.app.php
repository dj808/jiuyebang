<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/4/27
 * Time: 14:34
 */

/**
 * 话题控制器
 * User: dingj
 * Date: 2018/4/27
 * Time: 14:51
 */
class topicApp extends BackendApp {
    public $mod;
    public $ic;

    public function __construct () {
        parent::__construct();
        $this->mod =m('topic');
        false && $this->mod = new TopicMod();

        $this->ic = ic('topic');
        false && $this->ic = new Topic();
    }

    /**
     * @todo    话题列表
     * @author  dingj  (2018年04月27日)
     */
    public function ajax_list () {
        $cond[] = "mark=1";
        $search = trim($this->params['search']);

        if ($search)
            $cond[] = "`title` LIKE '%{$search}%'";

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
     * @todo   话题添加与编辑
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

        $this->assign('info', $info);
        $this->display();
    }
}


