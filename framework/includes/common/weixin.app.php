<?php
/**
 * 微信控制器基类
 * @author 刘小祥
 * @date 2016年3月17日
 */
class WeixinApp extends BaseApp
{

    public $userId, $secUserId, $openid;
    protected $userInfo;

    public function __construct($openid = null)
    {
        parent::__construct();

        //资源文件地址
        $this->data['assetsUrl'] = WAP_URL . "/assets";

        $this->initSession($openid);
        if ((APP == "ucenter" && ACT == "index") || (APP == "secAssoc" && ACT == "index")) {
            $this->checkLogin(APP); //验证登录
        }

    }

    /**
     * 初始化页面openid
     * @author 刘小祥
     * @date 2016年3月17日
     */
    public function initSession($openid = null)
    {
        //普通用户
        $this->userId = $_SESSION['userId'];
        if ($this->userId) {
            $userMod = &m("user");
            $userInfo = $userMod->getInfo($this->userId);
            $this->userInfo = $userInfo;

            $this->assign('userId', $this->userId);
            $this->assign('userInfo', $this->userInfo);
        }

        //小秘书
        $this->secUserId = $_SESSION['secUserId'];
        if ($this->secUserId) {
            $userMod = &m("aMember");
            $userInfo = $userMod->getInfo($this->secUserId);
            $this->userInfo = $userInfo;

            $this->assign('secUserId', $this->userId);
            $this->assign('userInfo', $this->userInfo);
        }

//            //获取Openid
        //            $wx = &ic('weixin');
        //            $openid = $wx->getPageOpenid();
        //            $this->openid = $openid;

    }

    /**
     *登录检测
     *@author 刘小祥
     *@date 2015-8-14
     *@return void
     */
    private function checkLogin($app)
    {
        if ($app == "ucenter" && !$_SESSION['userId']) {
            //普通用户
            header("location:?act=login");
        } else if ($app == "secAssoc" && !$_SESSION['secUserId']) {
            //小秘书登录
            header("location:?app=secUser&act=login");
        }
    }

    /**
     * 设置网站TDK
     * @author HH
     * @date 2016-5-23
     */
    public function setTdk($title, $description = false, $keywords = false)
    {
        $this->assign('title', $title);
        if ($keywords) {
            $this->assign('keywords', $keywords);
        }

        if ($description) {
            $this->assign('description', $description);
        }

    }

    /**
     *渲染模板
     *@author 刘小祥
     *@date 2015-6-25
     *@param string $tpl 模板名
     *@param array $data 待分配的变量
     */
    public function render($tpl = "", $data = array())
    {
        if (empty($tpl)) {
            $tpl = ACT . ".html";
        }

        foreach ($data as $name => $value) {
            $this->assign($name, $value);
        }
        $this->display("public/header.html");
        $this->display(APP . "/{$tpl}");
        $this->display("public/footer.html");
    }

    /**
     * 输出JSON数据
     * @author 刘小祥
     * @date 2016年3月8日
     */
    public function jsonReturn()
    {
        $arr = func_get_args();
        if (!is_array($arr[0])) {
            $result = call_user_func_array("message", $arr);
        } else {
            $result = $arr[0];
        }
        echo json_encode($result);
        exit();
    }

    public function test($msg = "", $append = true)
    {
        $app = APP;
        $act = ACT;
        if (empty($msg) && $append) {
            $msg = "{$app}::{$act} coming soon !";
        }
        $this->assign("msg", $msg);
        $this->display("default/test.html");
    }

    /**
     * 出错页面
     * @author 刘小祥
     * @date 2016年3月29日
     */
    public function errorPage($msg = "")
    {
        if (IS_POST) {
            $this->jsonReturn($msg, false);
        }
        if (!$msg) {
            $msg = "Page not found !";
        }

        $this->assign("url", pageUrl());
        $this->test($msg);
        exit();
    }
}
