<?php

class Common
{
    /**
     * 发送验证码
     */
    public static function send_sign($mobile, $sign_id = '38130')
    {
        if (!is_mobile($mobile)) {
            return R('9010');
        }
        $redis = &cache_server();
        $sign_key = 'sms:sign';

        $check_sign_json = $redis->server->hget($sign_key, $mobile);
        $check_sign = json_decode($check_sign_json, true);
        // 间隔一分钟才能再次发送
        if ($check_sign && $check_sign['etime'] - time() > 240) {
            return R('9013');
        }
        // 发送验证码
        $sign = get_nonce_str(4, 5);
        $content = '您本次操作的验证码是' . $sign . '，验证码5分钟内有效。如非本人操作，请忽略。';

        import('sms.class');
        $send_res = Sms::ningWangSend($mobile, $content, $sign_id);
        if ($send_res['status']) {
            // 验证码5分钟失效
            $etime = time() + 300;
            $redis->server->hset($sign_key, $mobile, json_encode(['sign' => $sign, 'etime' => $etime]));
        }

        $log_sms_mod = &m('LogSms');
        $log_sms_data = [
            'mobile' => $mobile,
            'message' => $content,
            'ip' => get_client_ip(),
            'code' => $send_res['data']['Code'],
            'response' => json_encode($send_res['data'], JSON_UNESCAPED_UNICODE),
        ];
        $log_sms_mod->edit($log_sms_data);
        return $send_res;
    }

    /**
     * 检查验证码
     */
    public static function check_sign($mobile, $sign)
    {
        if (!is_mobile($mobile)) {
            return R('9010');
        }
        // 验证码
        if (!$sign) {
            return R('9015');
        }
        
        //万能验证码
	    if($sign=='9999' )
		    return R('9900', true);
        
        
        $redis = &cache_server();
        $sign_key = 'sms:sign';

        $check_sign_json = $redis->server->hget($sign_key, $mobile);
        $check_sign = json_decode($check_sign_json, true);
        if ($sign != $check_sign['sign']) {
            return R('9016');
        }
        if (time() > $check_sign['etime']) {
            return R('9017');
        }
        return R('9900', true);
    }
}
