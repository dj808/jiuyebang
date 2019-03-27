<?php

/**
 * 首页控制器拓展
 * Created by Malcolm.
 * Date: 2018/4/12  19:36
 */
class Home extends Zeus {
    public $mod;

    public function __construct() {
        parent::__construct("user");

        $this->mod = m('user');
        false && $this->mod = new UserMod();
    }


    /**
     * @todo    获取开放城市
     * @author  Malcolm  (2018年04月12日)
     */
    public function getOpenCityList($param) {
        //默认南京
        $defaultCityId = 1388;
        $cityMod = m("city");
        $adcode = $param['adcode'];
        $cityInfo = [];

        //根据客户点设置对应城市
        if ($adcode) {
            $cityInfo = $cityMod->getRowByField("adcode" , $adcode);
            $cityId = $cityInfo['id'];
        }
        if (empty($cityInfo) || $cityInfo['is_open'] == 2) {
            $cityId = $defaultCityId;
        }

        //获取开放城市列表
        $provinceList = $cityMod->getData([
            'cond'     => "is_open=1 AND mark=1" ,
            'fields'   => "DISTINCT parent_id AS province_id" ,
            'order_by' => "sort ASC, id ASC"
        ]);
        $data = [];
        $provinceIds = [];
        $index = 0;
        foreach ($provinceList as $province) {
            $provinceId = $province['province_id'];
            $provinceInfo = $cityMod->getInfo($provinceId);
            $data[$index] = [
                'province_id' => $provinceInfo['id'] ,
                'name'        => $provinceInfo['name'] ,
                'is_open'     => 1 ,
                'list'        => []
            ];
            $cityList = $cityMod->getData([
                'cond'     => "parent_id={$provinceId} AND is_open=1 AND mark=1" ,
                'fields'   => "id AS city_id,name,adcode,is_open" ,
                'order_by' => "sort ASC, id ASC"
            ]);
            foreach ($cityList as $city) {
                $isDefault = $cityId == $city['city_id'] ? 1 : 2;
                $data[$index]['list'][] = [
                    'city_id'    => $city['city_id'] ,
                    'name'       => preg_replace("/市$/" , "" , $city['name']) ,
                    'adcode'     => $city['adcode'] ,
                    'is_open'    => $city['is_open'] ,
                    'is_default' => $isDefault
                ];
            }
            $index++;
        }

        return message('操作成功' , true , $data);
    }


    /**
     * @todo    获取首页信息
     * @author  Malcolm  (2018年04月12日)
     */
    public function getHomeInfo($param , $userId) {
        //设备
        $device = $param['device'];

        $android = false;

        if ($device == 2)
            $android = true;


        //获取首页广告
        $adList = m("ad")->getListByPosition('app_home_index_top' , $android);

        //今日分享
        $newsList = m('news')->getHomeList();

        //兼职列表
        $param['type'] = 2;
        $jobList = ic('job')->getList($param , $userId , true);
        
        //行业列表
        $industry_list = m('job_industry')->getData(['cond' => 'mark = 1 AND is_index = 1','limit' => '0,4','order_by' => 'sort DESC']);
        $data = [
            'ad_list'   => $adList ,
            'news_list' => $newsList ,
            'job_list'  => $jobList ,
            'industry_list' => $industry_list
        ];


        return message('操作成功' , true , $data);
    }

    /**
    * @todo    获取行业更多
    * @author Zhulx  (2018年08月03日)
    */
    public function getIndustry(){
        $info = m('job_industry')->getData(['cond' => 'mark = 1','order_by' => 'sort DESC']);
        return message('操作成功' , true , $info);
    }

    /**
     * @todo    添加收藏
     * @author  Malcolm  (2018年04月13日)
     */
    public function setCollect($param , $userId) {
        $type = intval($param['type']);
        $typeId = intval($param['type_id']);

        if (!$type || !$typeId)
            return message('参数丢失');

        $collectMod = m('collect');

        $cond = " type = {$type} AND type_id = {$typeId} AND  user_id = {$userId} AND mark = 1 ";
        $count = $collectMod->getCount($cond);

        if ($count)
            return message('操作成功' , true);

        $rs = $collectMod->edit([
            'type'    => $type ,
            'type_id' => $typeId ,
            'user_id' => $userId ,
        ]);

        if (!$rs)
            return message('系统繁忙，请稍候再试');

        return message('操作成功' , true , [
            'collect_id' => $rs
        ]);
    }


    /**
     * @todo    获取互助页面信息
     * @author  Malcolm  (2018年04月14日)
     */
    public function getCooperationPageInfo($param , $userId) {
        //广告
        $adList = m("ad")->getListByPosition('app_help_index_top');

        //列表
        $list = ic('cooperation')->getList($param , $userId , true);

        $data = [
            'ad_list'          => $adList ,
            'cooperation_list' => $list ,
        ];

        return message('操作成功' , true , $data);
    }


    /**
     * @todo    企业入住申请
     * @author  Malcolm  (2018年04月16日)
     */
    public function setCompanyApply($param , $userId) {
        $data['user_id'] = intval($userId);
        if (!$data['user_id'])
            return message('参数丢失');

        $companyMod = m('company');

        //判断用户类型
        $userType = m('user')->getUserType($userId);
        if ($userType != 1) {
            $companyId = $companyMod->getCompanyIdByUserId($userId);

            $companyInfo = $companyMod->getInfo($companyId);
            if ($companyInfo['status'] == 1)
                return message('您的申请已通过，无法重复申请');

            if ($companyInfo['status'] == 2)
                return message('您的申请已通过，无法重复申请');

        }


        $data['logo'] = Hera::upload("user_{$userId}/" , '' , $_FILES['logo']);

        $data['legal_person'] = trim($param['legal_person']);

        $data['name'] = trim($param['name']);

        $data['prov_id'] = intval($param['prov_id']);

        $data['city_id'] = intval($param['city_id']);

        $data['dist_id'] = intval($param['dist_id']);

        $data['address'] = trim($param['address']);

        $data['tel'] = trim($param['tel']);

        $data['link_person'] = trim($param['link_person']);

        $data['link_phone'] = trim($param['link_phone']);

        $data['business_license'] = Hera::upload("user_{$userId}/" , '' , $_FILES['business_license']);

        $type = intval($param['type']);

        if (!$type)
            return message('请选择企业类型');

        if (!$data['logo'])
            return message('请上传LOGO');

        if (!$data['legal_person'])
            return message('请输入法人姓名');

        if (!$data['name'])
            return message('请输入公司全称');

        if (!$data['prov_id'] || !$data['city_id'] || !$data['dist_id'])
            return message('请选在所在城市');

        if (!$data['address'])
            return message('请输入详细地址');

        if (!$data['link_person'])
            return message('请输入负责人姓名');

        if (!$data['link_phone'])
            return message('请输入负责人电话');

        if (!Zeus::isValidMobile($data['link_phone']))
            return message('请输入正确的负责人手机号');

        if (!$data['business_license'])
            return message('请上传企业营业执照');


        //判重
        $cond = " name = '{$data['name']}' AND mark = 1 ";
        $count = $companyMod->getCount($cond);
        if ($count)
            return message('该公司名称已申请，无法重复申请!');

        $companyMod->transStart();

        $rs = $companyMod->edit($data);
        if (!$rs) {
            $companyMod->transBack();
            return message('系统繁忙，请稍候再试');
        }


        //修改类型
        $rss = $this->mod->edit([
            'type' => $type
        ] , $userId);

        if (!$rss) {
            $companyMod->transBack();
            return message('系统繁忙，请稍候再试');
        }


        $companyMod->transCommit();


        //发送通知
        Zeus::sendMsg([
            'type'      => ['msg' , 'push'] ,
            'user_id'   => $data['user_id'] ,
            'title'     => "企业入住申请已提交" ,
            'content'   => "您已经成功提交企业入住申请，我司会尽快审核，并联系贵公司，请保持联系电话畅通！" ,
            'msg_type'  => 1 ,
            'user_type' => 1 ,
        ]);


        return message('操作成功' , true);
    }


    /**
     * @todo    获取搜索结果
     * @author  Malcolm  (2018年04月16日)
     */
    public function getSearchInfo($param , $userId) {
        $type = intval($param['type']);

        if (!$type)
            return message('参数丢失');

        switch ($type) {
            case 1:
                $mod = m('fun');
                break;

            case 2:
                $mod = m('raiders');
                $cond[] = " type = 3 ";
                break;

            case 3:
                $mod = m('raiders');
                $cond[] = " type = 2 ";
                break;

            case 4:
                $mod = m('raiders');
                $cond[] = " type = 1 ";
                break;

            case 5:
                $mod = m('news');
                break;

            default:
                $mod = m('fun');
        }


        $keyword = trim($param['keyword']);
        if (!$keyword)
            return message('请输入搜索关键词');


        //获取分词
        $newKey = Zeus::cutString($keyword);

        log::jsonInfo('分词服务返回');
        log::jsonInfo($newKey);
        log::jsonInfo('分词服务返回');

        if (!$newKey || !count($newKey))
            $newKey = $keyword;

        if ($newKey) {
            if (is_array($newKey)) {
                $tmp = [];
                foreach ($newKey as $key => $val) {
                    if ($val != '')
                        $tmp[] = " `title` LIKE '%{$val}%'  ";
                }

                if (count($tmp) == 1) {
                    $cond[] = $tmp[0];
                }
                else {
                    $tmp = implode(' OR ' , $tmp);

                    $cond[] = "( {$tmp} )";
                }

            }
            else {
                $cond[] = " `title` LIKE '%{$newKey}%'  ";
            }
        }

        $cond[] = " mark = 1  ";

        log::jsonInfo('条件');
        log::jsonInfo($cond);
        log::jsonInfo('条件');

        $data = $this->pageData([
            'cond'     => $cond ,
            'order_by' => ' id ASC'
        ] , $mod , 'getListInfo');

        return message('操作成功' , true , $data);
    }


    /**
     * @todo    获取搜索关键词
     * @author  Malcolm  (2018年04月16日)
     */
    public function getSearchPageKeyWords() {
        $list = Zeus::config('hot_keyword');

        $data = [];
        if (is_array($list)) {
            foreach ($list as $key => $val) {
                $data[] = [
                    'key_id'   => $key ,
                    'key_name' => $val ,
                ];
            }
        }

        return message('操作成功' , true , $data);
    }


    /**
     * @todo    获取推荐列表
     * @author  Malcolm  (2018年04月17日)
     */
    public function getRecommendList($param , $userId) {
        $cond = " mark = 1 ";
        $data = $this->pageData([
            'cond'     => $cond ,
            'order_by' => ' id DESC'
        ] , 'recommend' , 'getListInfo');

        return message('操作成功' , true , $data);
    }


    /**
     * @todo    新增培训订单
     * @author  Malcolm  (2018年06月14日)
     */
    public function setNewTrainOrder($param , $userId) {
        $trainingId = intval($param['training_id']);
        $realname = trim($param['realname']);
        $contactTel = trim($param['contact_tel']);
        $email = trim($param['email']);
        $message = trim($param['message']);

        if (!$trainingId)
            return message('参数丢失');

        if (!$realname)
            return message('请填写真实姓名');

        if (!$contactTel)
            return message('请填写联系手机号');

        if (!Zeus::isValidMobile($contactTel))
            return message('请填写正确的联系手机号');


        $trainMod = m('training');

        $info = $trainMod->getInfo($trainingId);

        if (!$info['title'])
            return message('参数错误');

        if ($info['status'] != 1)
            return message('改培训已下架，无法报名');

        if ($email && !Zeus::isValidEmail($email))
            return message('请输入正确的email邮箱');


        //组建数据
        $data = [
            'training_id'    => $trainingId ,
            'order_no'       => Zeus::getCodeByDate() ,
            'user_id'        => $userId ,
            'realname'       => $realname ,
            'contact_tel'    => $contactTel ,
            'email'          => $email ,
            'message'        => $message ,
            'pay_status'     => 1 ,
            'add_time'       => time() ,
            'cover'          => $info['cover'] ,
            'top_img'        => $info['top_img'] ,
            'price'          => $info['price'] ,
            'title'          => $info['title'] ,
            'sub_title'      => $info['sub_title'] ,
            'province_id'    => $info['province_id'] ,
            'city_id'        => $info['city_id'] ,
            'area_id'        => $info['area_id'] ,
            'address'        => $info['address'] ,
            'tel'            => $info['tel'] ,
            'class_num'      => $info['class_num'] ,
            'class_duration' => $info['class_duration'] ,
            'class_date'     => $info['class_date'] ,
            'class_intro'    => $info['class_intro'] ,
        ];

        //如果使用了优惠券
        $couponId = intval($param['user_coupon_id']);
        if ($couponId) {
            //判断是否可用优惠券
            if ($info['is_coupon'] == 2) {
                return message('该课程不可使用优惠券');
            }

            $userCouponMod = m('userCoupon');
            false && $userCouponMod = new UserCouponMod();

            //查询用户优惠券详情
            $couponInfo = $userCouponMod->getInfo($couponId);
            if (!$couponInfo)
                return message('该优惠券不可用');

            if (1 == $couponInfo['status'])
                return message('该优惠券已使用');

            if ($couponInfo['start_time'] > time())
                return message('该优惠券未到使用期限');

            if ($couponInfo['end_time'] < time())
                return message('该优惠券已过使用期限');


            $data['user_coupon_id'] = $couponId;
            $data['coupon_price'] = $couponInfo['coupon_price'];

            //是否为优惠券全额支付
            if ($couponInfo['coupon_price'] >= $info['price']) {
                $data['pay_type'] = 3;
                $data['total_fee'] = 0;
                $data['pay_status'] = 2;
                $data['pay_time'] = time();
            }
            else {
                $data['total_fee'] = intval($info['price']) - intval($data['coupon_price']);
            }

        }
        else {
            $data['total_fee'] = $info['price'];

            $data['user_coupon_id'] = 0;
            $data['coupon_price'] = 0;
        }


        //开始创建订单
        $trainingOrderMod = m('trainingOrder');
        false && $trainingOrderMod = new TrainingOrderMod();

        $trainingOrderMod->transStart();

        //插入培训订单表
        $rs = $trainingOrderMod->edit($data);

        if (!$rs) {
            $trainingOrderMod->transBack();
            return message('系统繁忙，请稍候再试');
        }

        //如果使用了优惠券 则维护用户优惠券
        if ($couponId) {
            $couponRs = $userCouponMod->edit(['status' => 1] , $couponId);
            if (!$couponRs) {
                $trainingOrderMod->transBack();
                return message('系统繁忙，请稍候再试');
            }
        }


        $trainingOrderMod->transCommit();


        Zeus::sendMsg([
            'type'      => ['msg' , 'push'] ,
            'user_id'   => $userId ,
            'title'     => '报名成功' ,
            'content'   => "您已成功报名课程:【{$info['title']}】,稍候会有客服人员联系您！" ,
            'msg_type'  => 1 ,
            'user_type' => 1 ,
        ]);


        $info = $trainingOrderMod->getOrderInfo($rs);

        return message('操作成功' , true , $info);
    }



    /**
     * @todo    新增点赞
     * @author Zhulx  (2018年07月20日)
     */
    public function setNewPraise($param , $userId){
        $type = intval($param['type']);
        $typeId = intval($param['type_id']);

        if(!$type || !$typeId)
            return message('参数丢失');
        
        //判断是否点赞
        $praise = m("praise")->getData(['cond' => "type = {$type} AND  type_id = {$typeId} AND user_id = {$userId}"])[0];
        if($praise['mark'] == 1){
            $isSend = true;
            //取消点赞
            m("praise")->drop($praise['id']);
            $msg = '已取消';
            $is_praise = 1;
        }else{
            //点赞
            $data = [
                'type' => $type,
                'type_id' => $typeId,
                'user_id' => $userId,
                'mark' => 1
            ];
            $resId = m('praise')->edit($data,$praise['id']);
            $msg = '点赞成功';
            $is_praise = 2;
            if($resId){
                $isSend = m('praise')->getInfo($resId)['is_send'];
            }else{
                return message('系统繁忙，请稍候再试');
            }
        }
        $num = 0;
        switch ($type){
            case 1:
                $infoMod = 'praise';
                $name = '趣事';
                //同步趣事点赞
                m('dynamic')->syncPraise($param['type_id'],$userId);
                $num = m('dynamic')->getInfo($param['type_id'])['praise_num'];
                break;
            case 2:
                $infoMod = 'comment';
                $name = '评论';
                //同步评论点赞
                m('comment')->syncPraise($param['type_id'],$userId);
                $num = m('comment')->getInfo($param['type_id'])['praise_num'];
                break;
            default:
                $infoMod = 'praise';
                $name = '趣事';
        }

        
        if(!$isSend){
            //发送通知
            $userInfo = $this->mod->getInfo($userId);
            Zeus::sendMsg([
                'type'      => ['msg' , 'push'] ,
                'user_id'   => $userInfo['id'] ,
                'title'     => "点赞提醒" ,
                'content'   => "{$userInfo['nickname']} ，给您发布的{$name}点了个大大的赞！" ,
                'msg_type'  => 1 ,
                'user_type' => 1 ,
            ]);
            $resId?m('praise')->edit(['is_send' => 1],$resId):'';  
        } 
        $data = [
            'praise_num' => $num,
            'is_praise' => $is_praise
        ];
        return message($msg , true, $data);
    }


}