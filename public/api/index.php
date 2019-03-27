<?php
	/*ini_set('display_errors','on');
	error_reporting(E_ALL^E_NOTICE^E_STRICT);*/
	//ini_set('display_errors','on');
	//error_reporting(E_ALL^E_NOTICE^E_STRICT);
	
	error_reporting(0);
	/*APP应用预定义常量*/
	define('API_KEY' , 'IaRt5201');
	define('PERPAGE' , 20);
	define('APP_PERPAGE' , 10);
	define('IS_API' , TRUE);
	/*应用程序根目录*/
	define('ROOT_PATH' , str_replace('\\' , '/' , dirname(dirname(dirname(__FILE__)))));
	define('LIB_PATH' , ROOT_PATH . '/framework/includes/libraries');
	
	
	
	/*引用核心文件*/
	include( ROOT_PATH . '/framework/eccore/debug.php' );
	include( ROOT_PATH . '/framework/eccore/ecmall.php' );
	include( ROOT_PATH . '/framework/eccore/request.php' );
	
	/*定义配置信息*/
	ecm_define(ROOT_PATH . '/framework/data/config.inc.php');
	
	
	define('APP_PATH' , ROOT_PATH . "/application/api");
	define('APP_ROOT' , APP_PATH . '/app');
	
	/*启动*/
	ECMall::startup([
		'default_app'   => 'default' ,
		'default_act'   => 'index' ,
		'app_root'      => APP_ROOT ,
		'external_libs' => [
			ROOT_PATH . '/framework/includes/global.lib.php' ,
			ROOT_PATH . '/framework/data/constant.php'
		]
	]);



