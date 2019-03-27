<?php
	
	/**
	 * 用户相关控制器
	 * Created by dingj
	 * Date: 2018/4/13  15:07
	 */
	class News extends Zeus
    {
        public $mod;

        public function __construct()
        {
            $this->mod = m('news');
            false && $this->mod = new NewsMod();

        }


        public function edit($param)
        {
            $id = (int) $param['id'];
            $title = trim($param['title']);
            $existId = $this->mod->getRowByField("title", $title, $id);
            if ($existId)
                return R("该标题已存在", false);

            if (!$title)
                return R("请输入标题", false);

            $cate_id = (int)$param['cate_id'];
            if (!$cate_id)
                return R('请选择分类', false);

            if (!$param['tag_ids'])
                return R('请选择标签', false);


            if (!$param['cover'])
                return R('请选择封面图', false);

            //格式化富文本
            if($param['content'])
                Zeus::saveFormatHtml($param['content']);

            $userinfo= session('userinfo');
            $param['add_user']=$userinfo['id'];

            $rs = $this->mod->edit($param, $id);
            if (!$rs) {
                return R("添加失败", false);
            }
            return R('操作成功', true);

        }


    }