<?php

/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/3/19
 * Time: 14:51
 */
/*
 * @todo 全职职位管理
 * @author dingj (2018年3月30日)
 */

class fullJobApp extends BackendApp {

    public $mod;
    public $company_mod;
    public $ic;

    public function __construct() {
        parent::__construct();
        $this->mod = m('job');
        false && $this->mod = new JobMod();

        $this->company_mod = m('company');
        false && $this->company_mod = new CompanyMod();

        $this->ic = ic('fulljob');
        false && $this->ic = new Fulljob();
    }

    /**
     * @todo   首页列表
     * @author dingj (2018年05月3日)
     */
    public function index() {
        //获取板块列表
        $typeList = $this->mod->typeList;
        $this->assign('typeList', $typeList);

        $companyList = $this->mod->getCompanyList();
        $this->assign('companyList', $companyList);
        parent::index();
    }

    /**
     * @todo    全职职位列表
     * @author  dingj (2018年3月30日)
     */
    public function ajax_list() {

        $cond[] = "mark=1 AND type=1 AND is_task=2";
        $search = trim($this->params['search']);
        //按标题搜索
        if ($search)
            $cond[] = "`title` LIKE '%{$search}%'";
        //按公司搜索
        if ($this->params['company_id'])
            $cond[] = "`company_id` LIKE '%{$this->params['company_id']}%'";
        //按薪水类型搜索
        if ($this->params['money_type'])
            $cond[] = "`money_type` LIKE '%{$this->params['money_type']}%'";

        $list = Zeus::pageData([
                    'cond' => $cond,
                    'order_by' => "id DESC"
                        ], $this->mod, 'getListInfo'
        );

        $this->ajaxReturn($list);
    }

    /**
     * @todo    全职职位添加与编辑
     * @author  dingj (2018年3月30日)
     */
    public function edit() {
        $id = $_REQUEST['id'];
        if (IS_POST) {
            $result = $this->ic->edit($_POST);
            $this->ajaxReturn($result);
        }
        //判断是编辑还是添加
        $info = [];
        if ($id) {
            $info = $this->mod->getInfo($id);
            if ($info['job_type_ids'])
                $info['job_type_ids'] = explode(',', $info['job_type_ids']);
            if ($info['tag_ids'])
                $info['tag_ids'] = explode(',', $info['tag_ids']);
        }
        $edulist = Zeus::config('edu_list');
        $job_time = Zeus::config('job_time_list');
        $job_type = Zeus::config('job_type_list');
        $job_tag = Zeus::config('job_tag');
        $job_industry = m('job_industry')->getData(['cond' => 'mark = 1']);

        $this->assign('info', $info);
        $this->assign('typeList', $this->mod->typeList);
        $this->assign('statusList', $this->mod->statusList);
        $this->assign('job_time', $job_time);
        $this->assign('edulist', $edulist);
        $this->assign('job_type', $job_type);
        $this->assign('job_tag', $job_tag);
        $this->assign('job_industry', $job_industry);
        $this->assign('companyList', $this->mod->getCompanyList());
        $this->display();
    }

    /**
     * @todo    删除数据
     * @author Malcolm  (2018年05月04日)
     */
    public function del() {
        $id = $this->params['id'];

        if (!$id)
            $this->ajaxReturn(R('参数丢失', false));

        $data = [
            'upd_user' => $this->uid,
            'upd_time' => time(),
            'mark' => -1,
        ];

        $res = $this->mod->edit($data, $id);

        if (!$res) {
            $this->ajaxReturn(R('9993'));
        }
        $this->ajaxReturn(R('9902', true));
    }

}
