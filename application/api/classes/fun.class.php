<?php


/**
 *  趣事控制器
 * Created by Malcolm.
 * Date: 2018/5/29  13:41
 */
class Fun extends Zeus {
    public $mod;
    public  function __construct() {
        parent::__construct("fun");

        $this->mod = m('fun');
        false&&$this->mod = new FunMod();
    }


    /**
     * @todo    获取趣事列表
     * @author Malcolm  (2018年05月29日)
     */
    public function getFunList($param,$isIn = false){
        $cond[] = " status <> 3 AND  mark = 1 ";

        $page = $perpage = $limit = 0;
        $this->initPage($page, $perpage, $limit);

        $query = [
            'fields' => 'id',
            'cond' => $cond,
            'limit' => $limit,
            'order_by' => ' id DESC ',
        ];

        $list = $this->mod->getData($query);
        $count = $this->mod->getCount($query['cond']);

        $newList = [];

        if ( is_array($list) ) {
            foreach ( $list as $key => $val ) {
                $newList[] = $this->mod->getListInfo($val['id']);
            }
        }

        $data = [
            'count'=>$count,
            'page'=>$page,
            'perpage'=>$perpage,
            'list'=>$newList
        ];

        if($isIn)
            return $data;


        return message('操作成功', true ,$data);
    }


    /**
     * @todo    获取校园趣事详情
     * @author Malcolm  (2018年05月29日)
     */
    public function getFunInfo($param,$userId){
        $funId = intval($param['fun_id']);
        if(!$funId)
            return message('参数丢失');

        $info = $this->mod->getDetailInfo($funId);

        //自动维护查看次数
        $this->mod->editColumnValue($funId,'look_num');


        //是否已收藏
        if($userId){
            $cond = " type = 3 AND type_id = {$funId} AND user_id = {$userId} AND mark = 1 ";
            $count = m('collect')->getCount($cond);

            $info['is_collect'] = $count?1:2;

            $info['collect_id'] = 0;

            if($info['is_collect']==1){
                $collectId = m('collect')->getIds($cond);
                $info['collect_id'] = $collectId[0];
            }
        }else{
            $info['collect_id'] = 0;
            $info['is_collect'] = 2;
        }


        //查询评论
        //留言
        $param['type'] = 6;
        $param['type_id'] = $funId;
        $param['parent_id'] = 0;

        $info['comment_list'] = ic('comment')->getList($param,$userId,true);

        return message('操作成功', true ,$info);
    }



}