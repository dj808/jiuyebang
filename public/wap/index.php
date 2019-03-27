<?php
// 报告所有错误

// 关闭错误报告
	ini_set('display_errors','on');
	error_reporting(E_ALL^E_NOTICE^E_STRICT^E_WARNING^E_DEPRECATED);

/*应用程序根目录*/
define('API_KEY', 'IaRt5201');
define('ROOT_PATH', str_replace('\\', '/', dirname(dirname(dirname(__FILE__)))));
define('APP_PATH', ROOT_PATH . '/application/wap');
define('APP_ROOT', ROOT_PATH . '/application/wap/app');
define('TPL_ROOT', ROOT_PATH . '/application/wap/template');
define('LIB_PATH', ROOT_PATH . '/framework/includes/libraries');
/*引用核心文件*/
include ROOT_PATH . '/framework/eccore/ecmall.php';
include ROOT_PATH . '/framework/eccore/request.php';
include ROOT_PATH . '/framework/eccore/debug.php';
include ROOT_PATH . '/framework/eccore/controller/base.app.php';
include ROOT_PATH . '/framework/eccore/controller/frontend.app.php';
/*定义配置信息*/
ecm_define(ROOT_PATH . '/framework/data/config.inc.php');

/*启动*/
ECMall::startup(array(
    'default_app' => 'index',
    'default_act' => 'index',
    'app_root' => APP_ROOT,
    'external_libs' => array(
        ROOT_PATH . '/framework/includes/global.lib.php',
        APP_ROOT . '/backend.app.php',
    ),
));
