<?php


/**
 *  宁网短信类
 * Created by Malcolm.
 * Date: 2018/5/18  16:35
 */
class SmsNw {
    /**
     * 发送短信
     * @param $mobile string 手机号
     * @param $content string 短信内容
     * @param $sign string(3-6个汉字) 签名
     * @return true
     */
    static function send($mobile, $content,$msgId, $sign="") {
        if (!$sign) $sign = "就业邦";

        $name = "jsjyb";
        $pwd = "111111";

        $newContent = $content."【{$sign}】";


        $array = array(
            'sname'=>$name,
            'spwd'=>$pwd,
            'sphones'=>trim($mobile),
            'smsg'=>trim($newContent),
            'msg_id'=>Zeus::getCodeById($msgId)
        );
        //$url = "http://223.68.139.178:9010/YidaInterface/SendSms.do?";
        $url = "http://221.226.28.36:9010/YidaInterface/SendSms.do?";
        $url .= http_build_query($array, null, "&");
        $response = @file_get_contents($url);

        return $response;
    }
}