<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/4/27
 * Time: 14:47
 */
	/**
     * 校园新鲜事相关控制器
     * Created by dingj
     * Date: 2018/6/13  15:07
     */
	class Dynamic extends Zeus
    {
        public $mod;

        public function __construct()
        {
            $this->mod = m('dynamic');
            false && $this->mod = new DynamicMod();

        }


        public function edit($param)
        {
            $id = (int) $param['id'];
            $info=$this->mod->getInfo($id);
            if(!$info['user_id']){
                $user_id = (int)$param['user_id'];
                if (!$user_id)
                    return R('请选择用户', false);
            }


            if (!$info['imgs'])
                return R('请选择图片', false);

            $rs = $this->mod->edit($param, $id);
            if (!$rs) {
                return R("添加失败", false);
            }
            //自动维护经纬度
            Zeus::push('gpsManage', ['type' => 'dynamic', 'typeId' => $id]);

            return R('操作成功', true);

        }

 }