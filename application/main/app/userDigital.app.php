<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/4/27
 * Time: 16:02
 */
/*
 * @todo 用戶积分记录管理
 * @author dingj (2018年4月27日)
 */
class userDigitalApp extends BackendApp
{
    public $mod;
    public $user_mod;
    public $cooperation_mod;
    public function __construct()
    {
        parent::__construct();
        $this->mod =m('userDigital');
        false && $this->mod = new userDigitalMod();

        $this->user_mod =m('user');
        false && $this->user_mod = new UserMod();

        $this->cooperation_mod =m('cooperation');
        false && $this->cooperation_mod = new CooperationMod();
    }

    /**
     * @todo    用戶积分记录列表
     * @author dingj (2018年3月31日)
     */
    public function ajax_list()
    {
        $cond[] ="mark=1";
        $search = trim($this->params['search']);
        if ($search){
            $tmp = "`nickname` LIKE '%{$search}%' AND mark = 1";
            $ids = $this->user_mod->getIds($tmp);
            $ids = implode(',' , $ids );
            $cond[] = "`user_id` IN ({$ids})";
        }

        if($this->params['classify'])
            $cond[] = "`classify` LIKE '%{$this->params['classify']}%'";

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
     * @todo    用戶积分记录添加与编辑
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
            $info = $this->mod->getJobInfo($id);
        }
        $this->assign('status', $info['status_name']);
        $this->assign('info', $info);

        $this->display();
    }

}


