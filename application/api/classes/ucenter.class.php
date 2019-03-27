<?php

/**
 * 个人中心  控制器
 * Created by Malcolm.
 * Date: 2018/2/2  15:18
 */
class Ucenter extends Zeus {
    public $mod;

    public function __construct() {
        parent::__construct("user");

        $this->mod = m('user');
        false && $this->mod = new UserMod();
    }


    /**
     * @todo    获取我的实名认证信息
     * @author  Malcolm  (2018年02月02日)
     */
    public function getAuthentication($userId) {
        $info = $this->mod->getAuthentication($userId);

        return message('操作成功' , true , $info);
    }


    /**
     * @todo    提交实名认证
     * @author  Malcolm  (2018年02月02日)
     */
    public function setAuthentication($param , $userId) {
        $userInfo = $this->mod->getInfo($userId);

        //判断当前实名认证状态，只有状态为1的时候 才可以提交审核
        if ($userInfo['real_status'] != 1)
            return message('您已提交认证资料，无法重复提交');

        $realname = trim($param['realname']);
        if (!$realname)
            return message('请输入真实姓名');

        $idcardNo = strtolower(trim($param['idcard_no']));
        if (!$idcardNo)
            return message('请输入您的身份证号码');

        if (!Zeus::isValidIdNo($idcardNo))
            return message('请输入正确的身份证号码');

        $temInfo = $this->mod->getRowByAttr(array(
            'idcard_no' => $idcardNo ,
        ));
        if ($temInfo['id'] && $temInfo['id'] != $userInfo['id']) {
            return message('该身份证号已被其他帐号实名认证，无法重复认证');
        }


        $idcardHandImg = Hera::upload("user_{$userId}/authentication" , '' , $_FILES['idcard_hand_img']);

        $idcardFaceImg = Hera::upload("user_{$userId}/authentication" , '' , $_FILES['idcard_face_img']);

        $idcardOppositeImg = Hera::upload("user_{$userId}/authentication" , '' , $_FILES['idcard_opposite_img']);


        $idcardHandImg = $idcardHandImg ? $idcardHandImg : $userInfo['idcard_hand_img'];
        $idcardFaceImg = $idcardFaceImg ? $idcardFaceImg : $userInfo['idcard_face_img'];
        $idcardOppositeImg = $idcardOppositeImg ? $idcardOppositeImg : $userInfo['idcard_opposite_img'];

        if (!$idcardHandImg)
            return message('请上传手持身份证照');

        if (!$idcardFaceImg)
            return message('请上传身份证正面照');

        if (!$idcardOppositeImg)
            return message('请上传身份证反面照');


        $data = [
            'real_status'         => 2 ,
            'realname'            => $realname ,
            'idcard_no'           => $idcardNo ,
            'idcard_face_img'     => $idcardFaceImg ,
            'idcard_opposite_img' => $idcardOppositeImg ,
            'idcard_hand_img'     => $idcardHandImg ,
        ];

        $rs = $this->mod->edit($data , $userId);

        if (!$rs)
            return message('系统繁忙，请稍候再试');

        $authData = $this->mod->getAuthentication($userId);

        //推送提醒
        Zeus::sendMsg([
            'type'      => ['msg' , 'push'] ,
            'user_id'   => $userId ,
            'title'     => '认证申请进度' ,
            'content'   => '您已成功提交认证资料，请耐心等候' ,
            'msg_type'  => 1 ,
            'user_type' => 1 ,
        ]);

        return message('您已成功提交认证资料，请耐心等候' , true , $authData);
    }


    /**
     * @todo    获取我的简历信息
     * @author  Malcolm  (2018年02月02日)
     */
    public function getMyResumeInfo($userId) {
        $info = m('resume')->getInfoByUserId($userId);
        return message('操作成功' , true , $info);
    }


    /**
     * @todo    编辑简历时，获取大学列表
     * @author  Malcolm  (2018年02月05日)
     */
    public function getCollegeList($keyword='') {
        $cond[] = "mark = 1";

	    if($keyword){
		    $cond[] = " `name` LIKE '%{$keyword}%' ";

		    $_POST['perpage'] = 1000;
		    $_REQUEST['perpage'] = 1000;

		    $_POST['page'] = 1;
		    $_REQUEST['page'] = 1;
	    }


        $data = $this->pageData([
            'cond'     => $cond ,
            'order_by' => ' id ASC'
        ] , 'college' , 'getListInfo');

        return message('操作成功' , true , $data);
    }








    /**
     * @todo    编辑简历时，获取专业列表
     * @author  Malcolm  (2018年02月05日)
     */
    public function getMajorList($param) {
        $pid = intval($param['pid']);
        if(!$param['pid']){
        	if(!$param['keyword'])
		        $cond[] = "pid = 0";
        }


        if ($pid)
            $cond[] = "pid = {$pid}";

        if($param['keyword']){
	        $cond[] = " `name` LIKE '%{$param['keyword']}%' ";
	        $_POST['perpage'] = 1000;
	        $_REQUEST['perpage'] = 1000;

	        $_POST['page'] = 1;
	        $_REQUEST['page'] = 1;
        }


        $cond[] = "mark = 1";

        $data = $this->pageData([
            'cond'     => $cond ,
            'order_by' => ' id ASC'
        ] , 'major' , 'getListInfo');

        return message('操作成功' , true , $data);
    }

    /**
     * @todo    编辑简历时，获取学历列表
     * @author  Malcolm  (2018年02月05日)
     */
    public function getEduList() {
        $list = Zeus::config('edu_list');

        $data = [];
        if (is_array($list)) {
            foreach ($list as $key => $val) {
                $data[] = [
                    'edu_id'   => $key ,
                    'edu_name' => $val ,
                ];
            }
        }

        return message('操作成功' , true , $data);
    }

    /**
     * @todo    编辑简历时，获取工作时间列表
     * @author  Malcolm  (2018年02月05日)
     */
    public function getJobTimeList() {
        $list = Zeus::config('job_time_list');

        $data = [];
        if (is_array($list)) {
            foreach ($list as $key => $val) {
                $data[] = [
                    'job_time_id'   => $key ,
                    'job_time_name' => $val ,
                ];
            }
        }

        return message('操作成功' , true , $data);
    }


    /**
     * @todo    编辑简历时，获取工作类型列表
     * @author  Malcolm  (2018年02月05日)
     */
    public function getJobTypeList() {
        $list = Zeus::config('job_type_list');

        $data = [];
        if (is_array($list)) {
            foreach ($list as $key => $val) {
                $data[] = [
                    'job_type_id'   => $key ,
                    'job_type_name' => $val ,
                ];
            }
        }

        return message('操作成功' , true , $data);
    }


    /**
     * @todo    编辑简历
     * @author  Malcolm  (2018年02月05日)
     */
    public function setMyResumeInfo($param , $userId) {
        $resumeMod = m('resume');
        $resumeInfo = $resumeMod->getInfoByUserId($userId);
        $name = trim($param['name']);
        $gender = intval($param['gender']);
        $graduated = intval($param['graduated']);

        //$birthday = trim($param['birthday'])?Hera::dateSwitch(trim($param['birthday'])):'';

        if ($param['birthday'])
            $birthday = date('Y-m-d' , strtotime($param['birthday']));

        $provinceId = intval($param['province_id']);
        $cityId = intval($param['city_id']);
        $areaId = intval($param['area_id']);
        $address = trim($param['address']);

        $tel = trim($param['tel']);


        $educationExp = $param['education_exp'];
        $jobExp = $param['job_exp'];

        $evaluation = trim($param['evaluation']);

        $studentPhoto = $_FILES['student_photo'] ? Hera::upload("user_{$userId}/resume" , '' , $_FILES['student_photo']) : $resumeInfo['student_photo'];
        $healthPhoto = $_FILES['health_photo'] ? Hera::upload("user_{$userId}/resume" , '' , $_FILES['health_photo']) : $resumeInfo['health_photo'];

        //基本资料判断必填
        if (!$name)
            return message('请输入真实姓名');

        if (!$gender)
            return message('请选择性别');

        if (!$graduated)
            return message('请选择人员类别');

        if (!$birthday)
            return message('请选择出生日期');

        if (!$provinceId || !$cityId || !$areaId)
            return message('请选择省市区');

        if (!$address)
            return message('请输入具体地址');

        if (!$tel)
            return message('请输入联系方式');

        if (!Zeus::isValidMobile($tel))
            return message('请输入正确的手机号');

        $data = [
            'user_id'       => $userId ,
            'name'          => $name ,
            'gender'        => $gender ,
            'graduated'     => $graduated ,
            'birthday'      => $birthday ,
            'province_id'   => $provinceId ,
            'city_id'       => $cityId ,
            'area_id'       => $areaId ,
            'address'       => $address ,
            'tel'           => $tel ,
            'education_exp' => serialize($educationExp) ,
            'job_exp'       => serialize($jobExp) ,
            'evaluation'    => $evaluation ,
            'student_photo' => $studentPhoto ,
            'student_no'    => $param['student_no'] ,
            'health_photo'  => $healthPhoto ,
            'health_no'     => $param['health_no'] ,
        ];

        $rs = $resumeMod->editByUser($data , $userId);

        if (!$rs)
            return message('系统繁忙，请稍候再试');

        return message('操作成功' , true);
    }


    /**
     * @todo    我得优惠券
     * @author  Malcolm  (2018年02月05日)
     */
    public function getMyCoupon($param , $userId) {
        $userCouponMod = m('userCoupon');

        $type = $param['type'];
        if (!$type)
            return message('参数丢失');

        $countCond = "  user_id = {$userId} AND status = 1 AND  mark = 1 ";
        $useNum = $userCouponMod->getCount($countCond); //已用

        $countCond = "  user_id = {$userId} AND status = 2 AND end_time > " . time() . "  AND  mark = 1 ";
        $noUseNum = $userCouponMod->getCount($countCond); //未用

        $countCond = "  user_id = {$userId} AND status = 2 AND end_time < " . time() . "  AND  mark = 1 ";
        $expiredNum = $userCouponMod->getCount($countCond); //过期


        if (1 == $type)
            $cond[] = " status = 1 ";

        if (2 == $type)
            $cond[] = " status = 2 AND end_time > " . time() . ' ';

        if (3 == $type)
            $cond[] = " status = 2 AND end_time < " . time() . ' ';

        $cond[] = "  user_id = {$userId}";

        $cond[] = "  mark = 1 ";


        $data = $this->pageData([
            'cond'     => $cond ,
            'order_by' => ' id ASC'
        ] , $userCouponMod , 'getListInfo');

        $data['use_num'] = $useNum;
        $data['no_use_num'] = $noUseNum;
        $data['expired_num'] = $expiredNum;


        return message('操作成功' , true , $data);
    }


    /**
     * @todo   我的消息列表
     * @author wangqs (2017年9月15日)
     */
    public function getMessage($userId) {
        $systemMessageMod = m("systemMessageRelation");
        false && $systemMessageMod = new SystemMessageRelationMod();
        $this->initPage($page , $perpage , $limit);
        $cond = "user_id={$userId} AND mark=1";
        $count = $systemMessageMod->getCount($cond);
        $list = $systemMessageMod->getData([
            'cond'     => $cond ,
            'fields'   => ['getInfo'] ,
            'limit'    => $limit ,
            'order_by' => "id DESC"
        ]);

        return message(MESSAGE_OK , true , ['count' => $count , 'page' => $page , 'perpage' => $perpage , 'list' => $list]);
    }


    /**
     * @todo    设置消息阅读状态
     * @author  wangqs  (2017年04月19日)
     */
    public function setMessageRead($param , $userId) {
        $systemMessageMod = m("systemMessageRelation");
        false && $systemMessageMod = new SystemMessageRelationMod();
        $systemMessageId = (int)$param['system_message_id'];
        if (!$systemMessageId) {
            return message(MESSAGE_PARAMETER_MISSING);
        }
        $rs = $systemMessageMod->setFieldValue("is_read" , 1 , $systemMessageId);
        if (!$rs) {
            return message(MESSAGE_SYSTEM_ERROR);
        }
        return message(MESSAGE_OK , true);
    }


    /**
     * @todo    获取我的发布
     * @author  Malcolm  (2018年02月07日)
     */
    public function getMyPush($param , $userId) {
        $tab = intval($param['tab']);
        if (!$tab)
            return message('参数丢失');

        if (1 == $tab)
            $mod = m('cooperation');
        else
            $mod = m('fun');

        if (1 == $tab) {
            $type = $param['type'];
            if (!$type)
                return message('参数丢失');

            $cond[] = " type = {$type} ";
        }


        $cond[] = " user_id = {$userId} AND mark = 1 ";
        $data = $this->pageData([
            'cond'     => $cond ,
            'order_by' => ' id ASC'
        ] , $mod , 'getMyListInfo');

        return message('操作成功' , true , $data);
    }


    /**
     * @todo    添加发布
     * @author  Malcolm  (2018年03月07日)
     */
    public function setPush($param , $userId) {
        $tab = intval($param['tab']);
        if (!$tab)
            return message('参数丢失');

        if (!in_array($tab , [1 , 2]))
            return message('参数错误');

        if (1 == $tab)
            $mod = m('cooperation');
        else
            $mod = m('fun');

        $cityMod = m('city');
        //参数处理
        if (1 == $tab) {
            $type = $param['type'];
            $needNum = intval($param['need_num']);
            $isPaid = $param['is_paid'];
            $sex = $param['sex'];
            $price = $param['price'];
            $title = $param['title'];
            $content = $param['content'];

            $lng = $param['lng'];
            $lat = $param['lat'];

            $cityArr = $cityMod->getAddressByGps($lng , $lat);
            $areaId = $cityArr['area_id'];


            if (!$type || !$lat || !$lng)
                return message('参数丢失');

            if (!$needNum)
                return message('请选择求助人数');

            if (!$isPaid)
                return message('请选择付费要求');

            if (!$sex)
                return message('请选择性别要求');

            if ($isPaid == 1 && !$price)
                return message('请输入付费金额');

            if (!$title)
                return message('请填写求助标题');

            if (!$content)
                return message('请填写求助内容');

            $data = [
                'user_id'  => $userId ,
                'type'     => $type ,
                'need_num' => $needNum ,
                'is_paid'  => $isPaid ,
                'sex'      => $sex ,
                'price'    => $price ,
                'title'    => $title ,
                'content'  => $content ,
                'area_id'  => $areaId ,
                'lng'      => $lng ,
                'lat'      => $lat ,
            ];

        }
        else {
            $title = $param['title'];
            $content = $param['content'];

            $img = Hera::upload("user_{$userId}/" , '' , $_FILES['images']);

            if (!$title)
                return message('请填写标题');

            if (!$content)
                return message('请填写内容');
            if (!$img)
                return message('最少上传一张图片作为封面图');


            $images = Hera::upload("user_{$userId}/attachment");

            $lng = $param['lng'];
            $lat = $param['lat'];

            $cityArr = $cityMod->getAddressByGps($lng , $lat);
            $areaId = $cityArr['area_id'];


            $data = [
                'user_id' => $userId ,
                'title'   => $title ,
                'content' => $content ,
                'images'  => $images ,
                'area_id' => $areaId ,
                'lng'     => $lng ,
                'lat'     => $lat ,
            ];
        }


        $rs = $mod->edit($data);
        if (!$rs)
            return message('系统繁忙，请稍候再试');

        return message('发布成功' , true);
    }


    /**
     * @todo    互助确认完成时，获取已申请列表
     * @author  Malcolm  (2018年03月09日)
     */
    public function getCooperChooseList($param , $userId) {
        $cooperationId = intval($param['cooperation_id']);
        if (!$cooperationId)
            return message('参数丢失');

        $mod = m('cooperationApply');

        $cond = " cooper_id = {$cooperationId} AND mark = 1 ";

        $data['list'] = [];
        $ids = $mod->getIds($cond);
        if (is_array($ids)) {
            foreach ($ids as $key => $val) {
                $data['list'][] = $mod->getMyListInfo($val);
            }
        }

        $data['count'] = count($ids);

        return message('操作成功' , true , $data);
    }


    /**
     * @todo    确认完成互助
     * @author  Malcolm  (2018年03月09日)
     */
    public function setPushFinish($param , $userId) {
        $cooperationId = intval($param['cooperation_id']);
        $applyUserId = intval($param['apply_user_id']);

        if (!$cooperationId || !$applyUserId)
            return message('参数丢失');

        $mod = m('cooperation');
        false && $mod = new CooperationMod();

        $mod->transStart();

        //维护主表
        $rs = $mod->edit(['status' => 2] , $cooperationId);
        if (!$rs) {
            $mod->transBack();
            return message('系统繁忙，请稍候再试');
        }

        //维护申请表
        $applyMod = m('cooperationApply');
        $cond = " cooper_id = {$cooperationId} AND user_id = {$applyUserId} AND mark = 1 ";
        $applyIds = $applyMod->getIds($cond);

        if (is_array($applyIds)) {
            foreach ($applyIds as $key => $val) {
                $rs = $applyMod->edit(['status' => 2] , $val);
                if (!$rs) {
                    $mod->transBack();
                    return message('系统繁忙，请稍候再试');
                }
            }
        }

        $mod->transCommit();

        $info = $mod->getInfo($cooperationId);

        //维护成长值(打赏方)
        $data = [
            'user_id' => $userId ,
            'num'     => 5 ,
            'type'    => 5 ,
            'todo'    => 'growth' ,
        ];
        Zeus::push('growth' , $data);

        //维护成长值（帮助者）
        if ($info['is_paid'] == 1 && $info['price'] > 0) {
            if (is_array($applyIds)) {
                foreach ($applyIds as $key => $val) {
                    $data = [
                        'user_id' => $val ,
                        'num'     => 5 ,
                        'type'    => 5 ,
                        'todo'    => 'growth' ,
                    ];
                    Zeus::push('growth' , $data);
                }
            }
        }

        return message('确认成功' , true);
    }


    /**
     * @todo    获取我的收藏
     * @author  Malcolm  (2018年04月10日)
     */
    public function getMyCollect($param , $userId) {
        $type = $param['type'];
        if (!$type)
            return message('参数丢失');

        $cond = " type = {$type} AND user_id = {$userId} AND mark = 1 ";

        $data = $this->pageData([
            'cond'     => $cond ,
            'order_by' => ' id DESC'
        ] , 'collect' , 'getListInfo');

        return message('操作成功' , true , $data);

    }

    /**
     * @todo    删除我的收藏
     * @author  Malcolm  (2018年04月19日)
     */
    public function dropMyCollect($param , $userId) {

        $id = intval($param['collect_id']);
        if (!$id)
            return message('参数丢失');

        $rs = m('collect')->drop($id);
        if (!$rs)
            return message('系统繁忙，请稍候再试');

        return message('操作成功' , true);
    }

    /**
     * @todo    签到
     * @author  Malcolm  (2018年04月11日)
     */
    public function sign($userId) {
        $signRecordMod = m("signRecord");
        false && $signRecordMod = new SignRecordMod();

        $lastInfo = $signRecordMod->getLastSignInfo($userId);
        $date = date("Y-m-d");
        $signInfo = $signRecordMod->getRowByAttr([
            'user_id'   => $userId ,
            'sign_date' => $date
        ]);
        if ($signInfo)
            return message('今日已签到');

        $num = 1;

        //如果是周末
        if ((date('w') == 6) || (date('w') == 0))
            $num = 2;


        $data = [
            'user_id'        => $userId ,
            'type'           => 1 ,
            'num'            => $num ,
            'sign_date'      => $date ,
            'continuity_num' => $lastInfo['continuity_num'] + 1 ,
            'total_num'      => $lastInfo['total_num'] + 1 ,
        ];

        $signRecordMod->transStart();

        $rs = $signRecordMod->edit($data);
        if (!$rs) {
            $signRecordMod->transBack();
            return message(MESSAGE_SYSTEM_ERROR);
        }


        //如果是连续签到
        if ($lastInfo['continuity_num'] > 0 && $lastInfo['continuity_num'] % 7 == 0) {
            $data = [
                'user_id'        => $userId ,
                'type'           => 2 ,
                'num'            => 5 ,
                'sign_date'      => $date ,
                'continuity_num' => $lastInfo['continuity_num'] ,
                'total_num'      => $lastInfo['total_num'] ,
            ];
            $rs = $signRecordMod->edit($data);
            if (!$rs) {
                $signRecordMod->transBack();
                return message(MESSAGE_SYSTEM_ERROR);
            }

            $num += 5;
        }

        $signRecordMod->transCommit();

        //维护成长值(打赏方)
        $data = [
            'user_id' => $userId ,
            'num'     => $num ,
            'type'    => 1 ,
            'todo'    => 'growth' ,
        ];
        Zeus::push('growth' , $data);

        return message('操作成功' , true , [
            'continuity_num' => $lastInfo['continuity_num'] + 1 ,
            'total_num'      => $lastInfo['total_num'] + 1 ,
        ]);

    }


    /**
     * @todo    获取我的兼职成长记录
     * @author  Malcolm  (2018年04月11日)
     */
    public function myJobList($param , $userId) {
        $cond = " type = 2 AND user_id = {$userId} AND status = 4 AND mark = 1 ";

        $data = $this->pageData([
            'cond'     => $cond ,
            'order_by' => ' id DESC'
        ] , 'jobApply' , 'getMyHalfJobList');

        $userInfo = $this->mod->getInfo($userId);

        $data['growth_total'] = $userInfo['growth_job'];

        return message('操作成功' , true , $data);
    }


    /**
     * @todo    获取我的培训成长记录
     * @author  Malcolm  (2018年04月11日)
     */
    public function myTrainingList($param , $userId) {
        $cond = "  user_id = {$userId} AND pay_status = 2 AND mark = 1 ";

        $data = $this->pageData([
            'cond'     => $cond ,
            'order_by' => ' id DESC'
        ] , 'trainingOrder' , 'myTrainingGrowthList');

        $userInfo = $this->mod->getInfo($userId);

        $data['growth_total'] = $userInfo['growth_train'];

        return message('操作成功' , true , $data);
    }


    /**
     * @todo    获取我的互助成长记录
     * @author  Malcolm  (2018年04月11日)
     */
    public function myHelpList($param , $userId) {
        $type = intval($param['type']);

        if ($type == 1)
            $cond[] = "to_user_id = {$userId}";
        else {
            $cond[] = "user_id = {$userId}";
        }

        $cond[] = " status = 2 AND mark = 1 ";


        $data = $this->pageData([
            'cond'     => $cond ,
            'order_by' => ' id DESC'
        ] , 'cooperationApply' , 'myHelpGrowthList');

        $userInfo = $this->mod->getInfo($userId);

        $data['growth_total'] = $userInfo['growth_help'];

        $mod = m('cooperationApply');
        //求助
        $data['help_count'] = $mod->getCount(" to_user_id = {$userId} AND status = 2 AND mark = 1 ");

        //帮助
        $data['for_help_count'] = $mod->getCount(" user_id = {$userId} AND status = 2 AND mark = 1 ");

        return message('操作成功' , true , $data);
    }

    /**
     * @todo    设置求职意向时，获取选择项
     * @author  Malcolm  (2018年04月11日)
     */
    public function getJobIntentionOption($userId , $isIn = false) {
        //职位类型
        $jobIntention = Zeus::config('job_type_list');

        $userInfo = $this->mod->getInfo($userId);

        $userIntention = explode(',' , $userInfo['job_intention']);
        $userZone = explode(',' , $userInfo['job_zone']);

        $intention = [];
        if (is_array($jobIntention)) {
            foreach ($jobIntention as $key => $val) {
                //判断是否选择了
                if (in_array($key , $userIntention))
                    $choose = 1;
                else
                    $choose = 2;

                $intention[] = [
                    'intention_id'   => $key ,
                    'intention_name' => $val ,
                    'is_choose'      => $choose ,
                ];
            }
        }

        //区域
        $jobZone = m('city')->getSubCity(1388);
        $zone = [];
        if (is_array($jobZone)) {
            foreach ($jobZone as $key => $val) {
                //判断是否选择了
                if (in_array($key , $userZone))
                    $choose = 1;
                else
                    $choose = 2;

                $zone[] = [
                    'zone_id'   => $key ,
                    'zone_name' => $val ,
                    'is_choose' => $choose ,
                ];
            }
        }

        $data = [
            'job_intention' => $intention ,
            'job_zone'      => $zone ,
        ];

        if ($isIn)
            return $data;

        return message('操作成功' , true , $data);
    }


    /**
     * @todo    设置求职意向
     * @author  Malcolm  (2018年04月11日)
     */
    public function setJobIntentionOption($param , $userId) {
        $jobIntention = $param['job_intention'];
        $jobZone = $param['job_zone'];

        if (count($jobIntention) > 5)
            return message('意向职位最多可选择5项');

        if (count($jobZone) > 5)
            return message('意向区域最多可选择5项');

        if (is_array($jobIntention) && count($jobIntention) > 0) {
            $intention = [];
            if (is_array($jobIntention)) {
                foreach ($jobIntention as $key => $val) {
                    $intention[] = $val['intention_id'];
                }
            }

            $intention = implode(',' , $intention);
        }
        else
            $intention = '';


        if (is_array($jobZone) && count($jobZone) > 0) {
            $zone = [];
            if (is_array($jobZone)) {
                foreach ($jobZone as $key => $val) {
                    $zone[] = $val['zone_id'];
                }
            }

            $zone = implode(',' , $zone);
        }
        else
            $zone = '';


        $data = [
            'job_intention' => $intention ,
            'job_zone'      => $zone ,
        ];

        $rs = $this->mod->edit($data , $userId);

        if (!$rs)
            return message('系统繁忙，请稍候再试');


        $data = $this->getJobIntentionOption($userId , true);

        return message('操作成功' , true , $data);
    }


    /**
     * @todo    获取我的职位申请列表
     * @author  Malcolm  (2018年04月11日)
     */
    public function getMyJobApplyList($param , $userId) {
        $type = intval($param['type']);
        if (!$type)
            return message('参数丢失');

        $cond = " type = {$type} AND is_task = 2 AND user_id = {$userId}  AND mark = 1";

        $data = $this->pageData([
            'cond'     => $cond ,
            'order_by' => ' id DESC'
        ] , 'jobApply' , 'getMyListInfo');

        return message('操作成功' , true , $data);
    }


    /**
     * @todo    我的任务
     * @author  Malcolm  (2018年04月12日)
     */
    public function getMyTaskInfo($userId) {
        //签到信息
        $signRecordMod = m("signRecord");
        false && $signRecordMod = new SignRecordMod();

        $jobApplyMod = m("jobApply");
        false && $jobApplyMod = new JobApplyMod();

        $lastInfo = $signRecordMod->getLastSignInfo($userId);

        $beginThisMonth = mktime(0 , 0 , 0 , date('m') , 1 , date('Y'));
        $endThisMonth = mktime(23 , 59 , 59 , date('m') , date('t') , date('Y'));

        //已领取兼职任务
        $cond = " type = 2 AND is_task = 1 AND user_id = {$userId} AND add_time >= {$beginThisMonth} AND add_time <= {$endThisMonth} AND mark = 1  ";

        $hasCount = $jobApplyMod->getCount($cond);

        //已完成
        $cond = " type = 2 AND is_task = 1 AND user_id = {$userId}  AND status = 4 AND add_time >= {$beginThisMonth} AND add_time <= {$endThisMonth} AND mark = 1  ";
        $finishCount = $jobApplyMod->getCount($cond);

        //未完成
        $cond = " type = 2 AND is_task = 1 AND user_id = {$userId}  AND status != 4 AND add_time >= {$beginThisMonth} AND add_time <= {$endThisMonth} AND mark = 1  ";
        $noFinishCount = $jobApplyMod->getCount($cond);


        return message('操作成功' , true , [
            'sign_total'    => $lastInfo['total_num'] ? $lastInfo['total_num'] : 0 ,
            'has_job'       => $hasCount ,
            'finish_job'    => $finishCount ,
            'no_finish_job' => $noFinishCount ,
        ]);

    }


    /**
     * @todo    获取可领取的兼职任务列表
     * @author  Malcolm  (2018年04月12日)
     */
    public function getJobTaskList($param , $userId) {
        $jobApplyMod = m("jobApply");
        false && $jobApplyMod = new JobApplyMod();

        //获取已经领取的任务
        $hasCond = " type = 2 AND is_task = 1 AND user_id = {$userId} AND status !=4 AND mark = 1 ";
        $ids = $jobApplyMod->getIds($hasCond);

        $hasJob = [];
        if (is_array($ids)) {
            foreach ($ids as $key => $val) {
                $info = $jobApplyMod->getInfo($val);
                $hasJob[] = $info['job_id'];
            }
        }

        $hasJob = implode(',' , $hasJob);

        //组合搜索条件
        $cond[] = " type = 2 AND is_task = 1 AND mark = 1 ";

        if ($hasJob)
            $cond[] = " NOT FIND_IN_SET(id,'{$hasJob}') ";


        $data = $this->pageData([
            'cond'     => $cond ,
            'order_by' => ' id DESC'
        ] , 'job' , 'getTaskListInfo');

        return message('操作成功' , true , $data);
    }


    /**
     * @todo    领取任务
     * @author  Malcolm  (2018年04月12日)
     */
    public function setNewTask($param , $userId) {
        $taskId = intval($param['task_id']);
        if (!$taskId)
            return message('参数丢失');

        $jobApplyMod = m("jobApply");
        false && $jobApplyMod = new JobApplyMod();

        //监测是否已接
        $cond = " job_id = {$taskId} AND type = 2 AND is_task = 1 AND user_id = {$userId} AND status !=4 AND mark = 1 ";

        $count = $jobApplyMod->getCount($cond);

        if ($count)
            return message('该任务已领取，无法重复领取');

        $jobInfo = m('job')->getInfo($taskId);

        $data = [
            'job_id'     => $taskId ,
            'type'       => 2 ,
            'is_task'    => 1 ,
            'company_id' => $jobInfo['company_id'] ,
            'user_id'    => $userId ,
            'status'     => 1 ,
            'snap'       => serialize($jobInfo) ,
        ];


        $rs = $jobApplyMod->edit($data);
        if (!$rs)
            return message('系统繁忙，请稍候再试');

        $userInfo = m('user')->getInfo($userId);

        //发送通知
        Zeus::sendMsg([
            'type'      => ['msg' , 'push'] ,
            'mobile'    => $userInfo['mobile'] ,
            'title'     => '任务领取成功' ,
            'content'   => "您已成功领取兼职任务【{$jobInfo['title']}】,请尽快完成后，在个人中心提交审核。" ,
            'msg_type'  => 1 ,
            'user_type' => 1 ,
        ]);

        return message('操作成功' , true);
    }


    /**
     * @todo    获取我的兼职任务列表
     * @author  Malcolm  (2018年04月12日)
     */
    public function getMyJobTaskList($param , $userId) {
        $tab = intval($param['tab']);

        if (!$tab)
            return message('参数丢失');

        $cond[] = " type = 2 AND is_task = 1 AND user_id = {$userId} AND mark = 1 ";
        if ($tab == 1)
            $cond[] = " status <3  ";
        else if ($tab == 2)
            $cond[] = " status =3  ";
        else
            $cond[] = " status >3  ";

        $data = $this->pageData([
            'cond'     => $cond ,
            'order_by' => ' id DESC'
        ] , 'jobApply' , 'getMyJobTaskListInfo');

        return message('操作成功' , true , $data);
    }


    /**
     * @todo    设置兼职任务完成
     * @author  Malcolm  (2018年04月12日)
     */
    public function setJobTaskFinish($param , $userId) {
        $id = intval($param['job_apply_id']);
        if (!$id)
            return message('参数丢失');

        $jobApplyMod = m("jobApply");
        false && $jobApplyMod = new JobApplyMod();

        //监测该任务的状态
        $info = $jobApplyMod->getInfo($id);
        if (!$info)
            return message('参数错误');

        if ($info['user_id'] != $userId)
            return message('参数错误');

        if ($info['status'] > 2)
            return message('操作成功' , true);

        $rs = $jobApplyMod->edit([
            'status' => 3
        ] , $id);

        if (!$rs)
            return message('系统繁忙，请稍候再试');

        $jobInfo = m('job')->getInfo($info['job_id']);

        //发送通知
        Zeus::sendMsg([
            'type'      => ['msg' , 'push'] ,
            'user_id'   => $userId ,
            'title'     => '兼职成功提交通知' ,
            'content'   => "您已成功提交兼职任务【{$jobInfo['title']}】结果审核,我们会尽快为你处理" ,
            'msg_type'  => 1 ,
            'user_type' => 1 ,
        ]);


        return message('操作成功' , true);
    }


    /**
     * @todo    获取个人资料
     * @author  Malcolm  (2018年04月17日)
     */
    public function getMyInfo($userId) {
        $info = ic('user')->getShortInfo($userId);
        return message('操作成功' , true , $info);
    }


    /**
     * @todo    资料是否完善
     * @author  Malcolm  (2018年05月26日)
     */
    public function isCompleteInfo($userId) {
        $info = m('resume')->getInfoByUserId($userId);

        $name = $info['name'];

        $school = $info['education_exp']['school_id'];

        $major = $info['education_exp']['major_id'];

        if (!$name || !$major || !$school)
            return message('操作成功' , true , ['is_complete' => 2]);


        return message('操作成功' , true , ['is_complete' => 1]);
    }


    /**
     * @todo    投票前获取简单资料
     * @author  Malcolm  (2018年05月26日)
     */
    public function getSimpleData($userId) {
        $info = m('resume')->getInfoByUserId($userId);


        $name = $info['name'] ? $info['name'] : '';

        $schoolId = $info['education_exp']['school_id'] ? $info['education_exp']['school_id'] : 0;
        $schoolName = $info['education_exp']['school_name'] ? $info['education_exp']['school_name'] : '';

        $majorId = $info['education_exp']['major_id'] ? $info['education_exp']['major_id'] : 0;
        $majorName = $info['education_exp']['major_name'] ? $info['education_exp']['major_name'] : '';


        return message('操作成功' , true , [
            'name'        => $name ,
            'school_id'   => $schoolId ,
            'school_name' => $schoolName ,
            'major_id'    => $majorId ,
            'major_name'  => $majorName ,
        ]);
    }


    /**
     * @todo    投票前完善简单资料
     * @author  Malcolm  (2018年05月26日)
     */
    public function setSimpleData($param , $userId) {
        $eduMod = m('resume');
        $info = $eduMod->getInfoByUserId($userId);

        $name = trim($param['name']);
        $schoolId = intval($param['school_id']);
        $schoolName = trim($param['school_name']);
        $majorId = intval($param['major_id']);
        $majorName = trim($param['major_name']);

        if (!$name)
            return message('请输入姓名');

        if (!$schoolId)
            return message('请选择学校');

        if (!$majorId)
            return message('请选择专业');


        $schoolInfo = m('college')->getInfo($schoolId);

        if ($schoolInfo['grade'] == 1) {
            $educationId = 5;
            $educationName = '本科';
        }
        else {
            $educationId = 4;
            $educationName = '专科';
        }


        $edu = [
            'education_id'   => $educationId ,
            'education_name' => $educationName ,
            'major_id'       => $majorId ,
            'major_name'     => $majorName ,
            'school_id'      => $schoolId ,
            'school_name'    => $schoolName ,
        ];

        $data = [
            'name'          => $name ,
            'education_exp' => serialize($edu) ,
        ];

        if(!$info['resume_id'])
            $data['user_id'] = $userId;

        $rs = $eduMod->edit($data , $info['resume_id'] ? $info['resume_id'] : 0);

        if (!$rs)
            return message('系统繁忙，请稍候再试');

        return message('操作成功' , true);
    }




    /**
     * @todo    获取我发布的职位信息列表
     * @author Malcolm  (2018年06月14日)
     */
    public function getMyPushJobList($param , $userId){

    }



}