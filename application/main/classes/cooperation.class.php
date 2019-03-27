<?php
	
	/**
	 * 互助相关控制器
	 * Created by dingj
	 * Date: 2018/4/23  15:07
	 */
	class Cooperation extends Zeus
    {
        public $mod;

        public function __construct()
        {
            $this->mod = m('cooperation');
            false && $this->mod = new CooperationMod();

        }


        public function edit($param)
        {
            $id = (int) $param['id'];

            $title = trim($param['title']);
            $existId = $this->mod->getRowByField("title", $title, $id);
            if ($existId)
                return R("该标题已存在", false);

            $sex = (int)$param['sex'];
            if (!$sex) {
                return R('请选择性別要求', false);
            }

            $type= (int)$param['type'];
            if (!$type) {
                return R('请选择类型', false);
            }
            $need_num= (int)$param['need_num'];
            if (!$need_num) {
                return R('请输入求助人数', false);
            }
            $is_paid= (int)$param['is_paid'];
            if (!$is_paid) {
                return R('请选择是否付費', false);
            }

            if($is_paid==1){
                $price = (float)($param['price']);
                if (!$price)
                    return R("请输入付费金额", false);
                $param['price']=$price;
            }else{
                $param['price']='';
            }

            $res = $this->mod->edit($param, $id);
            if (!$res) {
                return R("添加失败", false);
            }

            if (!$this->$id)
                $id = $res;
            else
                $id = $this->$id;

            //自动维护经纬度
            Zeus::push('gpsManage', ['type' => 'cooperation', 'typeId' => $id]);

            return R('操作成功', true);

        }

    }