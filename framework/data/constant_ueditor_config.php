<?php
return array(

    // ueditor 后端通信配置
    'UE_CONFIG' => [
        /*图片配置*/
        'imageActionName'        => 'upLoadImage' ,
        'imageFieldName'         => 'upfile' ,
        'imageMaxSize'           => 800480000 ,
        'imageAllowFiles'        => ['.png' , '.jpg' , '.jpeg' , '.gif' , '.bmp'] ,
        'imageCompressEnable'    => true ,
        'imageCompressBorder'    => '1600' ,
        'imageInsertAlign'       => 'none' ,
        'imageUrlPrefix'         => '' ,
        'imagePathFormat'        => '' ,
        /*涂鸦配置*/
        'scrawlActionName'       => 'uploadscrawl' ,
        'scrawlFieldName'        => 'upfile' ,
        'scrawlPathFormat'       => '' ,
        'scrawlMaxSize'          => 2048000 ,
        'scrawlUrlPrefix'        => '' ,
        'scrawlInsertAlign'      => 'none' ,
        /*截图工具上传*/
        'snapscreenActionName'   => 'upLoadImage' ,
        'snapscreenPathFormat'   => '' ,
        'snapscreenUrlPrefix'    => '' ,
        'snapscreenInsertAlign'  => 'none' ,
        /* 抓取远程图片配置 */
        'catcherLocalDomain'     => [IMG_URL] ,
        'catcherActionName'      => 'uploadFileByUrl' ,
        'catcherFieldName'       => 'source' ,
        'catcherPathFormat'      => '' ,
        'catcherUrlPrefix'       => '' ,
        'catcherMaxSize'         => 2048000 ,
        'catcherAllowFiles'      => ['.png' , '.jpg' , '.jpeg' , '.gif' , '.bmp'] ,
        /* 上传视频配置 */
        'videoActionName'        => 'uploadFile' ,
        'videoFieldName'         => 'upfile' ,
        'videoPathFormat'        => '' ,
        'videoUrlPrefix'         => '' ,
        'videoMaxSize'           => 102400000 ,
        'videoAllowFiles'        => [
            '.flv' , '.swf' , '.mkv' , '.avi' , '.rm' , '.rmvb' , '.mpeg' , '.mpg' ,
            '.ogg' , '.ogv' , '.mov' , '.wmv' , '.mp4' , '.webm' , '.mp3' , '.wav' , '.mid'
        ] ,

        /* 上传文件配置 */
        'fileActionName'         => 'uploadFile' ,
        'fileFieldName'          => 'upfile' ,
        'filePathFormat'         => '' ,
        'fileUrlPrefix'          => '' ,
        'fileMaxSize'            => 51200000 ,
        'fileAllowFiles'         => [
            '.png' , '.jpg' , '.jpeg' , '.gif' , '.bmp' ,
            '.flv' , '.swf' , '.mkv' , '.avi' , '.rm' , '.rmvb' , '.mpeg' , '.mpg' ,
            '.ogg' , '.ogv' , '.mov' , '.wmv' , '.mp4' , '.webm' , '.mp3' , '.wav' , '.mid' ,
            '.rar' , '.zip' , '.tar' , '.gz' , '.7z' , '.bz2' , '.cab' , '.iso' ,
            '.doc' , '.docx' , '.xls' , '.xlsx' , '.ppt' , '.pptx' , '.pdf' , '.txt' , '.xml'
        ] ,

        /* 列出指定目录下的图片 */
        'mageManagerActionName'  => 'istimage' ,
        'mageManagerListPath'    => '' ,
        'mageManagerListSize'    => 20 ,
        'mageManagerUrlPrefix'   => '' ,
        'mageManagerInsertAlign' => 'none' ,
        'mageManagerAllowFiles'  => ['.png' , '.jpg' , '.jpeg' , '.gif' , '.bmp'] ,
        /* 列出指定目录下的文件 */
        'ileManagerActionName'   => 'istfile' ,
        'ileManagerListPath'     => '' ,
        'ileManagerUrlPrefix'    => '' ,
        'ileManagerListSize'     => 20 ,
        'ileManagerAllowFiles'   => ['.png' , '.jpg' , '.jpeg' , '.gif' , '.bmp' , '.flv' , '.swf' , '.mkv' , '.avi' , '.rm' , '.rmvb' , '.mpeg' , '.mpg' , '.ogg' , '.ogv' , '.mov' , '.wmv' , '.mp4' , '.webm' , '.mp3' , '.wav' , '.mid' , '.rar' , '.zip' , '.tar' , '.gz' , '.7z' , '.bz2' , '.cab' , '.iso' , '.doc' , '.docx' , '.xls' , '.xlsx' , '.ppt' , '.pptx' , '.pdf' , '.txt' , '.md' , '.xml']

    ] ,
);
