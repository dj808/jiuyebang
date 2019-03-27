<?php
	
	/**
	 * 攻略秘籍相关控制器
	 * Created by dingj
	 * Date: 2018/4/13  15:07
	 */
	class Raiders extends Zeus
    {
        public $mod;
        public function __construct()
        {
            $this->mod = m('raiders');
            false && $this->mod = new RaidersMod();
        }

        public function edit($param)
        {
            $id = (int) $param['id'];

            $title = trim($param['title']);
            $existId = $this->mod->getRowByField("title", $title, $id);
            if ($existId)
                return R("标题名称已存在", false);
            if (!$title)
                return R("请输入标题名称", false);

            $param['price'] =(float)$param['price'];

            $cate_id = (int)$param['cate_id'];
            if (!$cate_id)
                return R('请选择所属分类', false);

            $type = (int)$param['type'];
            if (!$type)
                return R('请选择帐号类别', false);


           /* if (!$param['cover_img'])
                return R('请选择封面图', false);

            if (!$param['top_img'])
                return R('请选择详情顶部图', false);*/

            $param['parent_cate_id']=1;
            $param['is_choice']=$param['is_choice']== 'on' ? '1' : '2';
            $param['is_hot']=$param['is_hot']== 'on' ? '1' : '2';

            //格式化富文本
            if($param['content'])
                Zeus::saveFormatHtml($param['content']);

            $rs = $this->mod->edit($param, $id);
            if (!$rs) {
                return R("添加失败", false);
            }
            return R('操作成功', true);

        }


    }