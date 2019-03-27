<?php

class CompanyApp extends BackendApp
{
    public $mod;
    public $ic;

    public function __construct () {
        parent::__construct();


        $this->mod = m('company');
        false && $this->mod = new CompanyMod();

        $this->ic = ic('company');
        false && $this->ic = new Company();
    }
    /**
     * @todo   首页列表
     * @author dingj (2018年06月13日)
     */
    public function index () {
        //获取板块列表
        $typeList = $this->mod->typeList;
        $this->assign('typeList',$typeList);

        $statusList = $this->mod->statusList;
        $this->assign('statusList',$statusList);
        parent::index();
    }
    /**
     * @todo    公司列表
     * @author dingj (2018年3月15日)
     */
    public function ajax_list()
    {
        //根据条件搜索
        $cond[] ="mark=1";
        $search=trim($this->params['search']);
        //根据企业名称
        if ($search)
            $cond[] = "`name` LIKE '%{$search}%'";
        //根据类型
        if($this->params['type'])
            $cond[]= "`type` LIKE '%{$this->params['type']}%'";
        //根据状态
        if($this->params['status'])
            $cond[]= "`status` LIKE '%{$this->params['status']}%'";

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
     * @todo    公司添加与编辑
     * @author dingj (2018年3月15日)
     */
    public function edit()
    {
        $id=$_REQUEST['id'];
        if (IS_POST) {
            $result=$this->ic->edit($_POST, $id);
            $this->ajaxReturn($result);
        }
        //判断是编辑还是添加
        $info = [];
        if ($id) {
            $info = $this->mod->getInfo($id);
        }
        //企业用户
        $query=[
            'cond' => "type =2  AND mark = 1 ",
            'order_by' =>'id DESC'
        ];
        $userList=m('user')->getData($query);
        $userInfo = m('user')->getInfo($info['user_id']);
        $info['username']=$userInfo['nickname'];
        $this->assign('userList',$userList);
        $this->assign('statusList',$this->mod->statusList);
        $this->assign('info', $info);
        $this->display();
    }

    /**
     * @todo    公司详情
     * @author dingj (2018年3月15日)
     */
    public function detail()
    {
        $params = I('');
        //获取查询的一条用户记录
        $info = $this->mod->getInfo($params['id']);

        //地址
        $info['city_name']=m('city')->getCityName($info['dist_id']);

        $this->assign('info', $info);
       $this->display();
    }
}
