<?php


/**
 *  投票作品控制器
 * Created by Malcolm.
 * Date: 2018/6/21  00:27
 */
class OpusApp extends BackendApp {
    public $mod;

    public function __construct() {
        parent::__construct(true);
        $this->mod = m('opus');
        false && $this->mod = new OpusMod();

    }

    public function ajax_list() {
        $search = trim($this->params['search']);
        if ($search)
            $cond[] = " (`title` LIKE '%{$search}%' OR `school` LIKE '%{$search}%' ) ";

        $cond[] = "mark=1";

        $list = Zeus::pageData([
            'cond'     => $cond ,
            'order_by' => "id DESC"
        ] , $this->mod , 'getShortInfo');
        $this->ajaxReturn($list);
    }


    /**
     * @todo    新增编辑
     * @author Malcolm  (2018年06月21日)
     */
    public function edit(){
        $id = (int) $_REQUEST['id'];
        if(IS_POST) {
            $data['title'] = $this->params['title'];
            $data['school'] = $this->params['school'];
            $data['cover'] = $this->params['cover'];
            $data['detail'] = $this->params['detail'];


            if(!$data['title'])
                $this->ajaxReturn(R('请输入标题',false));

            if(!$data['school'])
                $this->ajaxReturn(R('请输入学校',false));

            if(!$data['cover'])
                $this->ajaxReturn(R('请上传封面图',false));

            if(!$data['detail'])
                $this->ajaxReturn(R('请输入详情',false));


            $rs = $this->mod->edit($data,$id);
            if(!$rs)
                $this->ajaxReturn(R('系统繁忙，请稍候再试',false));

            $this->ajaxReturn(R('操作成功',true));
        }
        //判断是编辑还是添加
        $info = [];
        if ($id)
            $info = $this->mod->getInfo($id);

        $this->assign('info', $info);
        $this->display();
    }


}