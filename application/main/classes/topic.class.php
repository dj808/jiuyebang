<?php
	
	/**
	 * 用户相关控制器
	 * Created by dingj
	 * Date: 2018/4/13  15:07
	 */
	class Topic extends Zeus
    {
        public $mod;

        public function __construct()
        {
            $this->mod = m('topic');
            false && $this->mod = new TopicMod();

        }


        public function edit($param)
        {
            $id = (int) $param['id'];
            $title = trim($param['title']);

            $existId = $this->mod->getRowByField("title", $title, $id);
            if ($existId)
                return R("该话题名称已存在", false);

            if (!$title)
                return R("请输入话题名称", false);

            if (!$param['logo'])
                return R('请选择话题logo', false);

            //格式化富文本
            if($param['introduce'])
                Zeus::saveFormatHtml($param['introduce']);


            $rs = $this->mod->edit($param, $id);
            if (!$rs) {
                return R("添加失败", false);
            }
            return R('操作成功', true);

        }


    }