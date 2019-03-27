<?php
/**
 * 广告控制器
 * User: dingj
 * Date: 2018/3/19
 * Time: 14:51
 */


class adApp extends BackendApp {
    public $mod;
    public function __construct() {
        parent::__construct();
        $this->mod = m('ad');
        false && $this->mod = new AdMod();
    }

    /**
     * @todo   首页列表
     * @author Malcolm  (2018年04月12日)
     */
    public function index() {
        //获取板块列表
        $positionList = $this->mod->positionList;
        $this->assign('positionList' , $positionList);

        parent::index();
    }


    /**
     * @todo    广告列表
     * @author  dj  (2018年03月19日)
     */
    public function ajax_list() {
        $cond[] = "mark=1";
        //查询
        $title = trim($this->params['title']);
        $position = trim($this->params['position']);
        if ($title)
            $cond[] = "`title` LIKE '%{$title}%'";

        if ($position)
            $cond[] = "`ad_position` LIKE '%{$position}%'";


        $data = Zeus::pageData([
            'cond'     => $cond ,
            'order_by' => "id DESC"
        ] ,
            $this->mod ,
            'getListInfo'
        );

        $this->ajaxReturn($data);
    }

    /**
     * @todo    广告添加与编辑
     * @author  dj  (2018年03月19日)
     */
    public function edit() {
        //获取广告位列表
        $positionList = $this->mod->positionList;
        $this->assign('positionList' , $positionList);

        //获取板块
        $plateList = $this->mod->plateList;
        $this->assign('plateList' , $plateList);


        $params = $this->params;
        if (IS_POST) {
            $data = [
                'ad_position'   => $params['ad_position'] ,
                'is_need_login' => $params['is_need_login'] ,
                'title'         => $params['title'] ,
                'type'          => $params['type'] ,
                'type_id'       => $params['type_id'] ,
                'plate'         => $params['plate'] ,
                'cover'         => $params['cover'] ,
                'content'       => $params['content'] ,
                'share_url'     => $params['share_url'] ,
            ];
            $res = $this->mod->edit($data , $params['id']);
            if (!$res) {
                $this->ajaxReturn(R('9993'));
            }
            $this->ajaxReturn(R('9901' , true));
        }
        //判断是编辑还是添加
        $info = [];
        if ($params['id']) {
            $info = $this->mod->getInfo($params['id']);
        }
        $this->assign('info' , $info);
        $this->display();
    }


    /**
     * @todo    根据板块获取各自板块列表
     * @author  Malcolm  (2018年04月12日)
     */
    public function getTypeIdList() {
        $type = $this->params['type'];
        $choose = $this->params['choose'];

        switch ($type) {
            case 1:
                $modName = 'job';
                $cond = 'type = 2 AND is_task = 2 AND mark = 1';
                break;

            case 2:
                $modName = 'job';
                $cond = 'type = 1 AND is_task = 2 AND mark = 1';
                break;

            case 3:
                $modName = 'training';
                $cond = ' mark = 1';
                break;

            case 4:
                $modName = 'cooperation';
                $cond = ' type = 1 AND  mark = 1';
                break;

            case 5:
                $modName = 'cooperation';
                $cond = ' type = 2 AND  mark = 1';
                break;

            case 6:
                $modName = 'raiders';
                $cond = '   mark = 1';
                break;

            case 7:
                $modName = 'fun';
                $cond = '   mark = 1';
                break;

            case 8:
                $modName = 'news';
                $cond = '   mark = 1';
                break;

            default:
                $modName = 'job';
                $cond = 'type = 2 AND is_task = 2 AND mark = 1';
        }

        $mod = m($modName);
        $ids = $mod->getIds($cond);

        $html = '';

        if (is_array($ids)) {
            foreach ($ids as $key => $val) {
                $info = $mod->getInfo($val);

                $html .= '<option value="' . $info['id'] . '" ';
                if ($choose && $choose == $info['id'])
                    $html .= '  selected  ';

                $html .= " > {$info['title']}</option>  ";
            }
        }

        $this->ajaxReturn(message('操作成功' , true , $html));
    }


}


