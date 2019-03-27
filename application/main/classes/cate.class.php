<?php
	
	/**
	 * 分类相关控制器
	 * Created by dingj
	 * Date: 2018/4/13  15:07
	 */
	class Cate extends Zeus
    {
        public $mod;
        public function __construct()
        {
            $this->mod = m('cate');
            false && $this->mod = new CateMod();

        }

        public function edit($param)
        {
            $id = (int) $param['id'];
	        
            $name = trim($param['name']);
            $existId = $this->mod->getRowByField("name", $name, $id);
            if ($existId)
                return R("该名称已存在", false);

            if (!$name )
                return R('请输入名称', false);

            $type = (int)$param['type'];
            if (!$type)
                return R("请输入所属类型", false);

            $sort = (int)$param['sort'];
            if (!$sort)
                return R("请输入排序", false);
	
	        $param['parent_id']=$type;
            $rs = $this->mod->edit($param, $id);
            
            if (!$rs) {
                return R("添加失败", false);
            }
            return R('操作成功', true);

        }


    }