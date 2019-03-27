<?php


/**
 *  layui UEditor 后端配置
 * Created by Malcolm.
 * Date: 2018/5/25  01:09
 */

class UeditorApp extends BackendApp {
    public $fileField; //文件域名
    public $file; //文件上传对象
    public $base64; //文件上传对象
    public $config; //配置信息
    public $oriName; //原始文件名
    public $fileName; //新文件名
    public $fullName; //完整文件名,即从当前配置目录开始的URL
    public $filePath; //完整文件名,即从当前配置目录开始的URL
    public $fileSize; //文件大小
    public $fileType; //文件类型
    public $stateInfo; //上传状态信息,
    public $stateMap = array( //上传状态映射表，国际化用户需考虑此处数据的国际化
                               "SUCCESS" , //上传成功标记，在UEditor中内不可改变，否则flash判断会出错
                               "文件大小超出 upload_max_filesize 限制" ,
                               "文件大小超出 MAX_FILE_SIZE 限制" ,
                               "文件未被完整上传" ,
                               "没有文件被上传" ,
                               "上传文件为空" ,
                               "ERROR_TMP_FILE"           => "临时文件错误" ,
                               "ERROR_TMP_FILE_NOT_FOUND" => "找不到临时文件" ,
                               "ERROR_SIZE_EXCEED"        => "文件大小超出网站限制" ,
                               "ERROR_TYPE_NOT_ALLOWED"   => "文件类型不允许" ,
                               "ERROR_CREATE_DIR"         => "目录创建失败" ,
                               "ERROR_DIR_NOT_WRITEABLE"  => "目录没有写权限" ,
                               "ERROR_FILE_MOVE"          => "文件保存时出错" ,
                               "ERROR_FILE_NOT_FOUND"     => "找不到上传文件" ,
                               "ERROR_WRITE_CONTENT"      => "写入文件内容错误" ,
                               "ERROR_UNKNOWN"            => "未知错误" ,
                               "ERROR_DEAD_LINK"          => "链接不可用" ,
                               "ERROR_HTTP_LINK"          => "链接不是http链接" ,
                               "ERROR_HTTP_CONTENTTYPE"   => "链接contentType不正确" ,
                               "INVALID_URL"              => "非法 URL" ,
                               "INVALID_IP"               => "非法 IP",
                               "FALSE"                    => "上传失败",
    );

    public function __construct() {
        parent::__construct();
    }


    /**
     * @todo    获取通信配置
     * @author  Malcolm  (2018年05月25日)
     */
    public function config() {
        $confStr = Conf::get('UE_CONFIG');

        $data = json_encode($confStr);

        $this->ueReturn($data);
    }


    /**
     * @todo    UE编辑器图片上传
     * @author  Malcolm  (2018年05月25日)
     */
    public function upLoadImage() {
        $this->stateMap['ERROR_TYPE_NOT_ALLOWED'] = iconv('unicode' , 'utf-8' , $this->stateMap['ERROR_TYPE_NOT_ALLOWED']);

        $file = $this->file = $_FILES;

        if (!$file) {
            $this->stateInfo = $this->getStateInfo("ERROR_FILE_NOT_FOUND");
            $this->ueReturn( $this->stateInfo );
        }
        if ($this->file['error']) {
            $this->stateInfo = $this->getStateInfo($file['error']);
            $this->ueReturn( $this->stateInfo );
        }

        $rs = Hera::upload('',1);

        if($rs['status'] != 1)
            $this->ueReturn( $this->getStateInfo("FALSE") );

        $img = explode('/',$rs['data'][0]);

        $img = end($img);

        $img = explode('.',$img);

        $title = $img[0];

        $type = $img[1];

        //处理返回参数
        $data = [
            'state' => 'SUCCESS',
            'url' => $rs['data'][0],
            'title' => $title,
            'original' => $_FILES['upfile']['name'],
            'type' => $type,
            'size' => $_FILES['upfile']['size'],
        ];

        $this->ueReturn( json_encode($data) );

    }


    /**
     * @todo    上传附件
     * @author Malcolm  (2018年05月25日)
     */
    public function uploadFile(){
        $this->stateMap['ERROR_TYPE_NOT_ALLOWED'] = iconv('unicode' , 'utf-8' , $this->stateMap['ERROR_TYPE_NOT_ALLOWED']);

        $file = $this->file = $_FILES;

        if (!$file) {
            $this->stateInfo = $this->getStateInfo("ERROR_FILE_NOT_FOUND");
            $this->ueReturn( $this->stateInfo );
        }
        if ($this->file['error']) {
            $this->stateInfo = $this->getStateInfo($file['error']);
            $this->ueReturn( $this->stateInfo );
        }

        $rs = Hera::uploadFile('',1);
        if($rs['status'] != 1)
            $this->ueReturn( $this->getStateInfo("FALSE") );

        $img = explode('/',$rs['data'][0]);

        $img = end($img);

        $img = explode('.',$img);

        $title = $img[0];

        $type = $img[1];

        //处理返回参数
        $data = [
            'state' => 'SUCCESS',
            'url' => $rs['data'][0],
            'title' => $title,
            'original' => $_FILES['upfile']['name'],
            'type' => $type,
            'size' => $_FILES['upfile']['size'],
        ];

        $this->ueReturn( json_encode($data) );
    }


    /**
     * @todo    保存外链图片
     * @author Malcolm  (2018年05月25日)
     */
    public function uploadFileByUrl(){

    }


    /**
     * @todo    错误检查
     * @author  Malcolm  (2018年05月25日)
     */
    private function getStateInfo($errCode) {
        return !$this->stateMap[$errCode] ? $this->stateMap["ERROR_UNKNOWN"] : $this->stateMap[$errCode];
    }


    /**
     * @todo    获取文件扩展名
     * @author  Malcolm  (2018年05月25日)
     */
    private function getFileExt() {
        return strtolower(strrchr($this->oriName , '.'));
    }

    private function getFullName() {
        //替换日期事件
        $t = time();
        $d = explode('-' , date("Y-y-m-d-H-i-s"));
        $format = $this->config["pathFormat"];
        $format = str_replace("{yyyy}" , $d[0] , $format);
        $format = str_replace("{yy}" , $d[1] , $format);
        $format = str_replace("{mm}" , $d[2] , $format);
        $format = str_replace("{dd}" , $d[3] , $format);
        $format = str_replace("{hh}" , $d[4] , $format);
        $format = str_replace("{ii}" , $d[5] , $format);
        $format = str_replace("{ss}" , $d[6] , $format);
        $format = str_replace("{time}" , $t , $format);

        //过滤文件名的非法自负,并替换文件名
        $oriName = substr($this->oriName , 0 , strrpos($this->oriName , '.'));
        $oriName = preg_replace("/[\|\?\"\<\>\/\*\\\\]+/" , '' , $oriName);
        $format = str_replace("{filename}" , $oriName , $format);

        //替换随机字符串
        $randNum = rand(1 , 10000000000) . rand(1 , 10000000000);
        if (preg_match("/\{rand\:([\d]*)\}/i" , $format , $matches)) {
            $format = preg_replace("/\{rand\:[\d]*\}/i" , substr($randNum , 0 , $matches[1]) , $format);
        }

        $ext = $this->getFileExt();
        return $format . $ext;
    }


    /**
     * @todo    获取文件名
     * @author Malcolm  (2018年05月25日)
     */
    private function getFileName() {
        return substr($this->filePath , strrpos($this->filePath , '/') + 1);
    }


    /**
     * @todo    获取文件完整路径
     * @author Malcolm  (2018年05月25日)
     */
    private function getFilePath() {
        $fullname = $this->fullName;
        $rootPath = $_SERVER['DOCUMENT_ROOT'];

        if (substr($fullname , 0 , 1) != '/') {
            $fullname = '/' . $fullname;
        }

        return $rootPath . $fullname;
    }


    /**
     * @todo    文件类型检测
     * @author Malcolm  (2018年05月25日)
     */
    private function checkType() {
        return in_array($this->getFileExt() , $this->config["allowFiles"]);
    }


    /**
     * @todo    文件大小检测
     * @author Malcolm  (2018年05月25日)
     */
    private function checkSize() {
        return $this->fileSize <= ($this->config["maxSize"]);
    }


    /**
     * @todo    获取当前上传成功文件的各项信息
     * @author Malcolm  (2018年05月25日)
     */
    public function getFileInfo() {
        return array(
            "state"    => $this->stateInfo ,
            "url"      => $this->fullName ,
            "title"    => $this->fileName ,
            "original" => $this->oriName ,
            "type"     => $this->fileType ,
            "size"     => $this->fileSize
        );
    }


    public function ueReturn($result) {
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/" , $_GET["callback"])) {
                echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            }
            else {
                echo json_encode(array(
                    'state' => 'callback参数不合法'
                ));
            }
        }
        else {
            echo $result;
        }
    }


}