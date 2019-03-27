<?php

/**
 * 默认控制器
 * @author 刘小祥 (2017年9月11日)
 */
class DefaultApp extends ApiApp {
    public $userIc;

    public function __construct() {
        parent::__construct(true);
        $this->userIc = ic('user');
        false && $this->userIc = new User();
    }


    /**
     * @todo    发送验证码
     * @author  Malcolm  (2018年01月31日)
     */
    public function sendSignCode() {
        $mobile = trim($this->req['mobile']);

        $type = intval(trim($this->req['type']));   //类型  1注册 2修改密码  3找回密码 4绑定

        if (!$mobile || !$type)
            $this->jsonReturn('参数丢失');

        if (!Zeus::isValidMobile($mobile))
            $this->jsonReturn('请输入正确的手机号');

        //注册验证码
        if ($type == 1) {
            $userMod = m("user");
            $userInfo = $userMod->getRowByAttr(array('mobile' => $mobile));
            if ($userInfo) {
                $this->jsonReturn(MESSAGE_MOBILE_REGISTERED);
            }
        }

        //修改密码
        if ($type == 2) {
            $this->needLogin();
            $mobile = $this->userInfo['mobile'];
        }

        //找回密码
        if ($type == 3) {
            $userMod = m("user");
            $userInfo = $userMod->getRowByAttr(array('mobile' => $mobile));
            if (!$userInfo) {
                $this->jsonReturn(MESSAGE_MOBILE_UNREGISTERED);
            }
        }
        //绑定
        if ($type == 4) {
            $userMod = m("user");
            $userInfo = $userMod->getRowByAttr(array('mobile' => $mobile));
            if (!$userInfo) {
                $this->jsonReturn(MESSAGE_MOBILE_UNREGISTERED);
            }
        }

        Zeus::sendSignSms($mobile);

        $this->jsonReturn('发送成功' , true);
    }


    /**
     * 登录
     * @author 刘小祥 (2017年3月1日)
     */
    public function login() {
        $result = $this->userIc->login($this->req);
        $this->jsonReturn($result);
    }

    /**
     * @todo    退出登录
     * @author  Malcolm  (2018年06月06日)
     */
    public function loginOut() {
        $this->needLogin();
        $result = $this->userIc->loginOut($this->userId);
        $this->jsonReturn($result);
    }


    /**
     * 注册
     * @author 刘小祥 (2017年3月1日)
     */
    public function register() {
        $result = $this->userIc->register($this->req);
        $this->jsonReturn($result);
    }

    /**
     * 重置密码
     * @author 刘小祥 (2017年3月2日)
     */
    public function resetPwd() {
        $result = $this->userIc->resetPwd($this->req);
        $this->jsonReturn($result);
    }


    /**
     * 第三方账号登录
     * @author 刘小祥 (2017年4月11日)
     */
    public function thirdAccountLogin() {
        $result = $this->userIc->thirdAccountLogin($this->req);
        $this->jsonReturn($result);
    }

    /**
     * 第三方账号绑定
     * @author 刘小祥 (2017年4月11日)
     */
    public function thirdAccountBind() {
        $result = $this->userIc->thirdAccountBind($this->req);
        $this->jsonReturn($result);
    }


    /**
     * 获取关于信息
     * @author 刘小祥 (2017年3月25日)
     */
    public function getAboutInfo() {
        $configMod = m("config");
        false && $configMod = new ConfigMod();
        //开机图
        $configList['open_img'] = $configMod->getInfoByTag('open_img');

        //客服电话
        $configList['hotline'] = $configMod->getInfoByTag('hotline');

        //关于我们
        $configList['about'] = $configMod->getInfoByTag('about');
        //帮助
        $configList['help'] = $configMod->getInfoByTag('help');
        //注册协议
        $configList['registrationProtocol'] = $configMod->getInfoByTag('registrationProtocol');

        //ios审核开关
        $configList['audit_ios_version'] = $configMod->getInfoByTag('audit_ios_version');
        //安卓审核开关
        $configList['android_audit_version'] = $configMod->getInfoByTag('android_audit_version');

        //广告开关
        $configList['ad_switch'] = $configMod->getInfoByTag('ad_switch');


        $this->jsonReturn('操作成功' , true , $configList);
    }

    /**
     * 获取版本更新信息
     * @author 刘小祥 (2017年3月28日)
     */
    public function getUpdateVersion() {
        $params = $this->req;
        if (!isset($params['device']) || !in_array(intval($params['device']) , array(1 , 2))) {
            $this->jsonReturn('参数丢失');
        }
        $versionMod = m("version");
        //获取针对当前版本更新信息
        if (isset($params['cur_version']) && trim($params['cur_version'])) {
            $cond = sprintf('version_type = 1 and update_version = \'%s\' and type = %d and mark = 1' , trim($params['cur_version']) , intval($params['device']));
            $query = array(
                'cond'     => $cond ,
                'order_by' => 'version_num DESC, add_time DESC'
            );
            $result = $versionMod->getOne($query);
        }

        if (!$result) {
            //没有针对该版本更新的信息，则获取全部的更新信息
            $cond = sprintf('version_type = 1 and update_version = \'\' and type = %d and mark = 1' , intval($params['device']));

            $query = array(
                'cond'     => $cond ,
                'order_by' => 'version_num DESC, add_time DESC'
            );

            $result = $versionMod->getOne($query);
        }

        if (is_array($result)) {
            if ($result['download']) {
                $url = $result['download'];
            }
            else {
                $url = '';
            }

            $data = array(
                'version'       => $result['version_num'] ,
                'intro'         => mb_strlen($result['intro'] , 'utf-8') ? preg_split('/[;\r\n]+/s' , $result['intro']) : array() ,
                'down_url'      => isset($url) ? $url : '' ,
                'time_interval' => $result['time_interval'] ? intval($result['time_interval']) : 0 ,
                'is_force'      => $result['is_force'] == 1 ? 1 : 2 ,
                'is_update'     => $result['is_update'] == 1 ? 1 : 2
            );
        }

        if (isset($data)) {
            $this->jsonReturn('操作成功' , true , $data);
        }
        else {
            $this->jsonReturn('操作成功' , true , new stdClass());
        }
    }


    /**
     * @todo    更新用户位置
     * @author  Malcolm  (2018年02月02日)
     */
    public function updUserLocation() {
        $result = $this->userIc->updUserLocation($this->req , $this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    获取广告列表
     * @author  Malcolm  (2018年06月15日)
     */
    public function getAdList() {
        $position = trim($this->req['position']);

        if (!$position)
            $this->jsonReturn('参数丢失');

        $device = $this->req['device'];

        $android = false;

        if ($device == 2)
            $android = true;


        $adList = m("ad")->getListByPosition($position , $android);

        $this->jsonReturn('操作成功' , true , ['ad_list' => $adList]);
    }


    /**
     * @todo    上传图片（单张）
     * @author Malcolm  (2018年06月20日)
     */
    public function uploadImage(){
        $rs = Hera::upload("image");

        $this->jsonReturn('操作成功',true,[
            'url' => $rs
        ]);
    }

}