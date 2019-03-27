<?php
	/**
	 * Created by PhpStorm.
	 * User: dj
	 * Date: 2018/3/19
	 * Time: 14:51
	 */

    /*
     * @todo 攻略、秘籍、试卷管理
     * @author dingj (2018年4月18日)
     */
	class raidersApp extends BackendApp
    {
        public $mod;
        public $cate_mod;
        public $ic;
        public function __construct()
        {
            parent::__construct();
            $this->mod =m('raiders');
            false && $this->mod = new RaidersMod();

            $this->cate_mod = m('cate');
            false && $this->cate_mod= new CateMod();
            $this->ic = ic('raiders');
            false && $this->ic = new Raiders();
        }
        /**
         * @todo   首页列表
         * @author dingj (2018年05月3日)
         */
        public function index () {
            //获取板块列表
            $cateList =$this->mod->getCateList();
            $this->assign('cateList',$cateList);
            parent::index();
        }
        /**
         * @todo    攻略、秘籍、试卷列表
         * @author dingj (2018年4月18日)
         */

        public function ajax_list()
        {
            $cond[] ="mark=1";
            $search=trim($this->params['search']);
            if ($search)
                 $cond[] = "`title` LIKE '%{$search}%'";

            if ($this->params['type'])
                $cond[] = "`type` LIKE '%{$this->params['type']}%'";

            $list = Zeus::pageData([
                        'cond' => $cond,
                        'order_by' => "id DESC"
                    ],
                        $this->mod,
                        'getRaidersInfo'
                    );
            $this->ajaxReturn($list);
       }

        /**
         * @todo    攻略、秘籍、试卷添加与编辑
         * @author dingj (2018年4月18日)
         */
        public function edit()
        {
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
        /**
         * @todo    根据板块获取各自板块列表
         * @author dingj  (2018年05月12日)
         */
        public function getTypeIdList(){
            $type = $this->params['type'];
            $choose = $this->params['choose'];
            switch ($type){
                case 1:
                    $cond = 'type = 1 AND mark = 1';
                    break;
                case 2:
                    $cond = 'type = 2 AND mark = 1';
                    break;
                case 3:
                    $cond = 'type = 3 AND mark = 1';
                    break;
                default:
                    $cond = 'type = 1 AND mark = 1';
            }

            $ids =$this->cate_mod->getIds($cond);
            $html = '';

            if ( is_array($ids) ) {
                foreach ( $ids as $key => $val ) {
                    $info =$this->cate_mod->getInfo($val);
                    $html .='<option value="'.$info['id'].'" ';
                    if($choose && $choose==$info['id'])
                        $html .='  selected  ';

                    $html .=" > {$info['name']}</option>  " ;
                }
            }

            $this->ajaxReturn(message('操作成功',true,$html));
        }

    }


