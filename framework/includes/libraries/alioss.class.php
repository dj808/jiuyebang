<?php
require_once ROOT_PATH . '/vendor/autoload.php';

use OSS\OssClient;

/**
 * 阿里云OSS操作
 * @author sunjian
 */
class Oss {

    /**
     * 图片上传
     */
    public static function upload($dir = 'tmp' , $fileName = '' ) {
        $dir = DB_PREFIX . $dir;

        if ($fileName)
            $file = $fileName;
        else
            $file = current($_FILES);


        if (!$file) {
            return R('9006');
        }


        if (!$file || !is_array($file)) {
            return R('9991');
        }

        if ($file['error']) {
            switch ($file['error']) {
                case '1':
                    $error = '文件超过允许的大小。';
                    break;
                case '2':
                    $error = '超过表单允许的大小。';
                    break;
                case '3':
                    $error = '图片只有部分被上传。';
                    break;
                case '4':
                    $error = '请选择图片。';
                    break;
                case '6':
                    $error = '找不到临时目录。';
                    break;
                case '7':
                    $error = '写文件到硬盘出错。';
                    break;
                case '8':
                    $error = 'File upload stopped by extension';
                    break;
                default:
                    $error = '未知错误';
            }
            return R($error);
        }

        $file_name = $file['name'];
        $file_type = $file['type'];
        $file_size = $file['size'];
        $file_tmp_name = $file['tmp_name'];

        // 文件格式check
        if (!in_array($file_type , ['image/jpeg' , 'image/pjpeg' , 'image/png' , 'image/x-png' , 'image/bmp'])) {
            return R('9007');
        }

        // 文件大小check
        if ($file_size > 100 * 1024 * 1024 * 1024) {
            return R('9008');
        }

        //检查是否已上传
        if (@is_uploaded_file($file_tmp_name) === false) {
            return R('9009');
        }

        $object = ($dir ? $dir . '/' : '') . md5(uniqid(mt_rand() , true)) . '.' . strtolower(trim(pathinfo($file_name)['extension']));

        try {
            $ossClient = new OssClient(ALIYUN_ACCESS_KEY_ID , ALIYUN_ACCESS_KEY_SECRET , ALIYUN_OSS_ENDPOINT);
            $ossClient->putObject(ALIYUN_BUCKET_IMG , $object , file_get_contents($file_tmp_name));

            return R('9900' , true , [IMG_URL . '/' . $object]);
        } catch (OssException $e) {
            log::jsonInfo('############');
            log::jsonInfo($e->getMessage());
            log::jsonInfo('############');
            return R($e->getMessage());
        }
    }




    /**
     * 判断文件是否存在
     */
    public static function file_exist($filepath) {
        $exist = false;
        try {
            $ossClient = new OssClient(ALIYUN_ACCESS_KEY_ID , ALIYUN_ACCESS_KEY_SECRET , ALIYUN_OSS_ENDPOINT);
            $exist = $ossClient->doesObjectExist(ALIYUN_BUCKET_IMG , $filepath);
        } catch (OssException $e) {
            // printf(__FUNCTION__ . ": FAILED\n");
            // printf($e->getMessage() . "\n");
            // return;
        }
        return $exist;
    }


    /**
     * @todo    上传附件
     * @author  Malcolm  (2018年05月25日)
     */
    public static function uploadFile($dir = 'tmp' , $fileName = '') {
        $dir = DB_PREFIX . $dir;

        if ($fileName)
            $file = $fileName;
        else
            $file = current($_FILES);


        if (!$file) {
            return R('9006');
        }


        if (!$file || !is_array($file)) {
            return R('9991');
        }

        if ($file['error']) {
            switch ($file['error']) {
                case '1':
                    $error = '文件超过允许的大小。';
                    break;
                case '2':
                    $error = '超过表单允许的大小。';
                    break;
                case '3':
                    $error = '只有部分被上传。';
                    break;
                case '4':
                    $error = '请选择文件。';
                    break;
                case '6':
                    $error = '找不到临时目录。';
                    break;
                case '7':
                    $error = '写文件到硬盘出错。';
                    break;
                case '8':
                    $error = 'File upload stopped by extension';
                    break;
                default:
                    $error = '未知错误';
            }
            return R($error);
        }

        $file_name = $file['name'];

        $file_size = $file['size'];
        $file_tmp_name = $file['tmp_name'];


        $type1 = [
            'flv' , 'swf' , 'mkv' , 'avi' , 'rm' , 'rmvb' , 'mpeg' , 'mpg' ,
            'ogg' , 'ogv' , 'mov' , 'wmv' , 'mp4' , 'webm' , 'mp3' , 'wav' , 'mid'
        ];


        $type2 = [
            'png' , 'jpg' , 'jpeg' , 'gif' , 'bmp' ,
            'flv' , 'swf' , 'mkv' , 'avi' , 'rm' , 'rmvb' , 'mpeg' , 'mpg' ,
            'ogg' , 'ogv' , 'mov' , 'wmv' , 'mp4' , 'webm' , 'mp3' , 'wav' , 'mid' ,
            'rar' , 'zip' , 'tar' , 'gz' , '7z' , 'bz2' , 'cab' , 'iso' ,
            'doc' , 'docx' , 'xls' , 'xlsx' , 'ppt' , 'pptx' , 'pdf' , 'txt' , 'xml'
        ];


        $type = array_merge($type1 , $type2);

        $file_type = explode('.' , $file_name);

        $file_type = end($file_type);


        // 文件格式check
        if (!in_array($file_type , $type)) {
            return R('9007');
        }

        // 文件大小check
        if ($file_size > 50 * 1024 * 1024) {
            return R('9008');
        }

        //检查是否已上传
        if (@is_uploaded_file($file_tmp_name) === false) {
            return R('9009');
        }

        $object = ($dir ? $dir . '/' : '') . md5(uniqid(mt_rand() , true)) . '.' . strtolower(trim(pathinfo($file_name)['extension']));

        try {
            $ossClient = new OssClient(ALIYUN_ACCESS_KEY_ID , ALIYUN_ACCESS_KEY_SECRET , ALIYUN_OSS_ENDPOINT);
            $ossClient->putObject(ALIYUN_BUCKET_IMG , $object , file_get_contents($file_tmp_name));

            return R('9900' , true , [IMG_URL . '/' . $object]);
        } catch (OssException $e) {
            return R($e->getMessage());
        }
    }


    /**
     * @todo    上传本地资源
     * @author Malcolm  (2018年05月25日)
     */
    public static function uploadFileByLocal($path,$dir = 'attachment') {
        $dir = DB_PREFIX . $dir;
        $dateDir = date("/Y/m/d/H");

        $dir .= $dateDir;

        $object = ($dir ? $dir . '/' : '') . md5(uniqid(mt_rand() , true)) . '.' . strtolower(trim(pathinfo($path)['extension']));

        try {
            $ossClient = new OssClient(ALIYUN_ACCESS_KEY_ID , ALIYUN_ACCESS_KEY_SECRET , ALIYUN_OSS_ENDPOINT);
            $ossClient->uploadFile(ALIYUN_BUCKET_IMG, $object, $path);

            return R('9900' , true , [IMG_URL . '/' . $object]);
        } catch (OssException $e) {
            return R($e->getMessage());
        }
    }


    /**
     * @todo    批量上传图片
     * @author Malcolm  (2018年06月20日)
     */
    public static function uploads($arrName){
        $dir = DB_PREFIX . 'attachment';

        $dateDir = date("/Y/m/d/H");

        $dir .= $dateDir;

        $allowedExts = array("jpg", "jpeg", "gif", "png");
        $fileData = $_FILES[$arrName];
        $fileList = $fileData['tmp_name'];

        $ossClient = new OssClient(ALIYUN_ACCESS_KEY_ID , ALIYUN_ACCESS_KEY_SECRET , ALIYUN_OSS_ENDPOINT);

        $rs = [];

        foreach ($fileList  as $key=>$row) {
            if ($fileData['error'][$key]!==0) {
                continue;
            }

            $filename = $fileData['name'][$key];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if (!in_array($ext, $allowedExts)) {
                continue;
            }

            $object = ($dir ? $dir . '/' : '') . md5(uniqid(mt_rand() , true)) . '.' . strtolower(trim(pathinfo($filename)['extension']));

            try {
                $ossClient->putObject(ALIYUN_BUCKET_IMG , $object , file_get_contents($fileData['tmp_name'][$key]));

                $rs[] = IMG_URL . '/' . $object;
            } catch (OssException $e) {
                return R($e->getMessage());
            }

            return $rs;
        }

    }



}
