<?php
/**
 * 常用函数类
 * @author 刘小祥 (2016年12月23日)
 */
class Tool
{
    /**
     * 接口内部通信
     * @author 刘小祥 (2016年11月11日)
     */
    public static function getInterResponse($data = array())
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? "https" : "http";
        $url = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/index.php";
        $ch = curl_init($url);
        //log::jsonInfo("Request url is : {$url}");
        curl_setopt($ch, CURLOPT_POST, true);
        //log::json($data);
        if ($data) {
            $crypt = getCryptDesObject();
            $data = $crypt->encrypt(json_encode($data));
            $data = http_build_query(array('APIDATA' => $data), null, "&");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseText = curl_exec($ch);
        $curlInfo = curl_getinfo($ch);
        //log::jsonInfo("curl error is :" .curl_error($ch));
        //log::jsonInfo($curlInfo);

        //log::jsonInfo("Response1 text is : {$responseText}");
        $responseText = $crypt->decrypt($responseText, API_KEY);
        //log::jsonInfo("Response2 text is : {$responseText}");
        $info = json_decode($responseText, 1);
        //log::jsonInfo($info);
        if (!$info) {
            $info = array();
            $info['code'] = "90001";
            $info['msg'] = "网络异常:" . curl_error($ch);
        }
        return $info;
    }

}
