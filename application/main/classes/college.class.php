<?php
	
	/**
	 * 用户相关控制器
	 * Created by dingj
	 * Date: 2018/4/13  15:07
	 */
	class College extends Zeus
    {
        public $mod;

        public function __construct()
        {
            $this->mod = m('college');
            false && $this->mod = new CollegeMod();
        }

        public function edit($param)
        {
            $id = (int) $param['id'];

            $name = trim($param['name']);
            $existId = $this->mod->getRowByField("name", $name, $id);
            if ($existId)
                return R("该名称已存在", false);
            if(!$name)
                return R("请填写学校名称", false);

            $grade = (int)$param['grade'];
            if (!$grade)
                return R('请选择类别', false);


            $is_private = (int)$param['is_private'];
            if (!$is_private)
                return R('请选择是否民办', false);


            $is_211 = (int)$param['is_211'];
            if (!$is_211)
                return R('请选择是否是211院校', false);

            $is_985 = (int)$param['is_985'];
            if (!$is_985)
                return R('请选择是否是985院校', false);


            $rs = $this->mod->edit($param, $id);
            if (!$rs) {
                return R("添加失败", false);
            }
                 return R('操作成功', true);

        }


    }