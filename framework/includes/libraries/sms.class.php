<?php

class Sms
{

    /**
     * 普通短信/定时短信
     * @param $mobile 用户发送的短信号码，多个号码以半角逗号分隔
     * @param $content 用户发送的自定义短信内容，建议长度不要超过200字
     * @param $sign_id 短信签名建议2-8字符，id可在管理后台获取、添加、编辑
     * @param $send_time 定时短信发送时间戳，日期格式的需要转化成时间戳
     */
    public static function feige_send($mobile, $content, $sign_id = '38130', $send_time = 0 ,$isIn = false)
    {
        $url = 'http://api.feige.ee/SmsService/Send';
        $params = [
            'Account' => SMS_FEIGE_ACCOUNT,
            'Pwd' => SMS_FEIGE_PWD,
            'Content' => $content,
            'Mobile' => $mobile,
            'SignId' => $sign_id,
        ];
        $timeout = 20;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); //在HTTP请求中包含一个"User-Agent: "头的字符串。
        curl_setopt($curl, CURLOPT_HEADER, 0); //启用时会将头文件的信息作为数据流输出。
        curl_setopt($curl, CURLOPT_POST, true); //发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params); //Post提交的数据包
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); //启用时会将服务器服务器返回的"Location: "放在header中递归的返回给服务器，使用CURLOPT_MAXREDIRS可以限定递归返回的数量。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); //文件流形式
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout); //设置cURL允许执行的最长秒数。
        $data = curl_exec($curl);
        curl_close($curl);
        unset($curl);

        $data_arr = json_decode($data, true);
        if (0 == $data_arr['Code']) {
            $res = R('9011', true, $data_arr);
        } else {
            //如果发送错误，则换短信通道
            if(!$isIn){
                self::ningWangSend($mobile, $content,'',true);
            }else{
                $res = R($data_arr['Message'], false, $data_arr);
            }

        }

        log::jsonInfo('##原短信通道##');
        log::jsonInfo($res);
        log::jsonInfo('##原短信通道##');
        return $res;
    }


    /**
     * @todo    宁网短信通道
     * @author Malcolm  (2018年05月18日)
     */
    public static function ningWangSend($mobile, $content, $sign="" ,$isIn = false){
        if (!$sign) $sign = "就业邦";
        if($sign=='38130') $sign = "就业邦";

        $name = "jsjyb";
        $pwd = "111111";

        $newContent = $content."【{$sign}】";

        $array = array(
            'sname'=>$name,
            'spwd'=>$pwd,
            'sphones'=>trim($mobile),
            'smsg'=>trim($newContent),
            'msg_id'=>Zeus::getCodeById(rand())
        );
        //$url = "http://223.68.139.178:9010/YidaInterface/SendSms.do?";
        $url = "http://221.226.28.36:9010/YidaInterface/SendSms.do?";
        $url .= http_build_query($array, null, "&");
        $response = @file_get_contents($url);

        $rs = explode(',',$response);

        //如果发送错误，则换短信通道
        if($rs[0] != 0){
            if(!$isIn){
                self::feige_send($mobile, $content,'38130','',true);
            }else{
                $res = R('系统繁忙，请稍候再试', false, []);
                log::jsonInfo('##宁网短信通道2##');
                log::jsonInfo($res);
                log::jsonInfo('##宁网信通道2##');

                return $res;
            }
        }

        log::jsonInfo('##宁网短信通道##');
        log::jsonInfo($response);
        log::jsonInfo('##宁网信通道##');
        return $res = R('9011', true, []);
    }


}
