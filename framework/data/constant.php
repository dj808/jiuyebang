<?php
$constant_1 = array(
    'UPLOAD_PIC' => [
        'maxSize' => 10 * 1024 * 1024, // 10M
        'exts' => ['jpg', 'gif', 'png', 'jpeg'],
        'autoSub' => true, //自动子目录保存文件
        'subName' => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => '.' . ROOT_PATH . '/public/uploads/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
    ],
    'ACCESS_NOCHECK_CONTROLLER' => [
        'backend',
        'index',
        'login',
    ],
    'ACCESS_NOCHECK_ACTION' => [
        'index',
        '_initialize',
        '__construct',
        'ajax_list',
    ],
);

$constant_2 = require_once 'constant_message.php';

$constant_3 = require_once 'constant_ueditor_config.php';

// 返回合并过的配置参数
return array_merge($constant_1, $constant_2,$constant_3);
