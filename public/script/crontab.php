<?php
// 关闭错误报告
	//ini_set('display_errors','on');
	//error_reporting(E_ALL^E_NOTICE^E_STRICT);
/*应用程序根目录*/
define('API_KEY', 'IaRt5201');
define ('ROOT_PATH', str_replace('\\', '/', dirname(dirname(dirname(__FILE__)))));
define ('APP_PATH', ROOT_PATH. '/application/script');
define ('APP_ROOT', ROOT_PATH . '/application/script/app');
define ('TPL_ROOT', ROOT_PATH . '/application/script/template');
define ('LIB_PATH', ROOT_PATH . '/framework/includes/libraries');
define ('TEMP_PATH', ROOT_PATH. '/application/script/temp');
/*引用核心文件*/
include (ROOT_PATH . '/framework/eccore/debug.php');
include (ROOT_PATH . '/framework/eccore/ecmall.php');
include (ROOT_PATH . '/framework/eccore/request.php');
/*定义配置信息*/
ecm_define (ROOT_PATH . '/framework/data/config.inc.php');
	
	do_get();
	
/*启动*/
ECMall::startup(array(
	'default_app' 	=> 	'crontab',
	'default_act'	=>	'index',
	'app_root'		=>	APP_ROOT,
	'external_libs' =>  array(
        ROOT_PATH 	. '/framework/includes/global.lib.php',
        ROOT_PATH 	. '/framework/data/constant.php',
        APP_ROOT    . '/baseScript.app.php',
    )
));

