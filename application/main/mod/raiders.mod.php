<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/4/8
 * Time: 14:52
 */
class RaidersMod extends CBaseMod
{

        /**
         * 构造函数
         */
        public function __construct()
        {
            parent::__construct('raiders');

           /* $this->cateList=m('cate')->getData([
                'cond'=>"type!=4 AND parent_id=0 AND mark=1",
                'order_by'=>"id DESC"
            ]);*/
        }

        /**
         * @todo    获取 攻略、秘籍、试卷信息
         * @author dingj  (2018年05月15日)
         */
        public function getRaidersInfo($id)
        {
            $info=parent::getInfo($id);
            $info['add_time'] = $info['add_time'] ? date('Y-m-d H:i:s', $info['add_time']) : '';//添加时间
            $info['upd_time'] = $info['upd_time'] ? date('Y-m-d H:i:s', $info['upd_time']) : '';//更新时间
            $info['type'] = $info['type'] == 3 ? '攻略' : ($info['type'] == 1 ? '秘籍' : '试卷');//类别的判断
            $info['is_choice']=$info['is_choice']==1 ? '是':'否';
            $info['is_hot']=$info['is_hot']==1 ? '是':'否';
            $cate_name=$this->cateMod->getInfo($info['cate_id']);
            $info['cate_id']=$cate_name['name'];
            return $info;
        }
        /**
         * @todo    获取 攻略、秘籍、试卷列表
         * @author dingj  (2018年05月15日)
         */
        public function getCateList(){
            $cond[] = "type!=4 AND parent_id!=4 AND mark = 1";

            $cateMod= $this->cateMod;
            $ids = $cateMod->getIds($cond);
            $data = [];
            if ( is_array($ids) ) {
                foreach ( $ids as $key => $val ) {
                    $info = $cateMod->getInfo($val);
                    $data[] = [
                        'id' => $info['id'],
                        'name' => $info['name'],
                    ];
                }
            }

            return $data;
        }

}