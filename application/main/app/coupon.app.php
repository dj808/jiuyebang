<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/3/26
 * Time: 10:40
 */

/**
 * 优惠券控制器
 * @author dingj (2018年3月26日)
 */
 class couponApp extends backendApp{

     public $mod;
     public $ic;
     public function __construct(){

         parent::__construct();
         $this->mod=m('coupon');
         false && $this->mod = new CouponMod();

         $this->ic= ic('coupon');
         false && $this->ic = new CouponMod();
     }
     /**
      * @todo   首页列表
      * @author dingj (2018年05月3日)
      */
     public function index () {
         //获取板块列表
         $typeList = $this->mod->typeList;
         $this->assign('typeList',$typeList);

         $statusList = $this->mod->statusList;
         $this->assign('statusList',$statusList);

         parent::index();
     }

     /**
      * @todo    优惠券列表
      * @author dingj (2018年3月18日)
      */
     public function ajax_list(){
        $cond[]="mark=1";
        $name=trim($this->params['name']);
        if($name)
            $cond[]="`name` LIKE '%{$name}%'";

         //按优惠券类型
         if ($this->params['type'])
             $cond[] = "`type` LIKE '%{$this->params['type']}%'";

         //按优惠券类型
         if ($this->params['status'])
             $cond[] = "`status` LIKE '%{$this->params['status']}%'";

        $list=Zeus::pageData([
                'cond'=>$cond
                ],
                 $this->mod,
                'getCouponInfo'
        );

        $this->ajaxReturn($list);
     }
     /**
      * @todo    优惠券添加与编辑
      * @author dingj (2018年3月18日)
      */
      public function edit(){
          $id = (int) $_REQUEST['id'];

          if(IS_POST) {
              $result = $this->ic->edit($_POST, $id);
              $this->ajaxReturn($result);
          }
          $info=[];
          if($id)
          $info=$this->mod->getInfo($id);

          $this->assign('info',$info);
          $this->display();
      }

 }