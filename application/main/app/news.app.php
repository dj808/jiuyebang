<?php
	/**
	 * Created by PhpStorm.
	 * User: dj
	 * Date: 2018/3/19
	 * Time: 14:51
	 */

    /**
     * @todo 资讯管理
     * @author dingj (2018年4月24日)
     */
	class newsApp extends BackendApp
    {
        public $mod;
        public $cat_mod;
        public $ic;

        public function __construct()
        {
            parent::__construct(true);
            $this->mod =m('news');
            false && $this->mod = new NewsMod();

            $this->cat_mod =m('cate');
            false && $this->cat_mod = new CateMod();

            $this->ic = ic('news');
            false && $this->ic = new News();

        }
        /**
     * @todo   首页列表
     * @author dingj (2018年05月3日)
     */
        public function index () {
            //获取板块列表
            $cateList = $this->mod->getCateList();
            
            $this->assign('cateList',$cateList);
            parent::index();
        }
        /**
         * @todo    资讯列表
         * @author dingj (2018年4月25日)
         */
        public function ajax_list()
        {
            $cond[] ="mark=1";
            $search=trim($this->params['search']);
            if ($search)
                $cond[] = "`title` LIKE '%{$search}%'";

            //按所属分类搜索
            if ($this->params['cate_id'])
                $cond[] = "`cate_id` LIKE '%{$this->params['cate_id']}%'";

            $list = Zeus::pageData([
                    'cond' => $cond,
                    'order_by' => "id DESC"
                 ],
                    $this->mod,
                    'getNewsInfo'
                 );
            $this->ajaxReturn($list);
       }

        /**
         * @todo    用戶添加与编辑
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
            $cate_name=$this->mod->getCateList();
            $news_tag= Zeus::config('news_tag');

            $this->assign('info', $info);
            $this->assign('news_tag', $news_tag);
            $this->assign('cate_name', $cate_name);
            $this->display();
        }

    }


