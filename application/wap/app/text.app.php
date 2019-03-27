<?php


/**
 *  测试
 * Created by Malcolm.
 * Date: 2018/5/18  16:30
 */
class TextApp extends FrontendApp{
    public $id;

    public function __construct() {
        parent::__construct();

        $this->id = $this->params['id'];
    }


    /**
     * @todo    测试短信
     * @author Malcolm  (2018年05月18日)
     */
    public function index(){
        $phone = $this->params['phone'];

        $text = "测试短信发送";

        import('smsNw.lib');

        $sms = new SmsNw();

        $rs = $sms->send($phone,$text,rand());

        printd($rs);

    }


}