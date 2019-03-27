<?php
	/**
	 * 网站配置文件
	 */
	return array (
		//*******************公共URL部分-Start******************//
		'SITE_NAME'         => '就业邦',
		//'MAIN_URL'          => 'http://main.njyb.test',
        'MAIN_URL'          => 'http://www.jyb.com',
		'IMG_URL'           => 'http://yun-attachment.oss-cn-hangzhou.aliyuncs.com',
		'MILIEU'            => 'RD',    //RD：开发环境  TEST：测试环境  SERVER：生产环境
		'DB_CONFIG'         => 'mysql://root:swx@2018%khy@115.28.179.119:3306/jiuyebang',
		'DB_PREFIX'         => 'njyb_',
		'DB_CHARSET'        => 'utf8mb4',
		'CHARSET'           => 'utf-8',
		'ATTACHEMENT_PATH'  => ROOT_PATH . "/attachment" ,
		'TEMP_PATH'         => ROOT_PATH . "/temp" ,
		'DEF_CAPTCHA'       => 1 ,
		'DEBUG_MODE'        => 0,
		'MKEY'              => "njyb", //缓存KEY的前缀
		
		// cache配置
		'CACHE_CONFIG' => 'redis://:@127.0.0.1:6379/1',
		// 'CACHE_SERVER'      => 'memcached',
		// //'CACHE_SERVER'        => 'default',
		// 'CACHE_HOST'        => '127.0.0.1',
		// 'CACHE_PORT'        => '11211',
		// 'CACHE_TIME'        => 3600*24*7,// 默认缓存7天
		
		
		/**
		 * 阿里云
		 */
		'ALIYUN_ACCESS_KEY_ID' => 'LTAI69Bzb414zh3T',
		'ALIYUN_ACCESS_KEY_SECRET' => 'P8W2vvgkk5sHMaWrsUNyof1p5Ljq5U',
		'ALIYUN_OSS_ENDPOINT' => 'oss-cn-hangzhou.aliyuncs.com',
		'ALIYUN_REGION_ID' => 'cn-hangzhou',
		'ALIYUN_BUCKET_IMG' => 'yun-attachment', // 阿里云OSS，图片Bucket
		
		/**
		 * 短信（飞鸽传书）
		 */
		'SMS_FEIGE_ACCOUNT' => 'jiuye',
		'SMS_FEIGE_PWD' => '2652f1abb79f4062d35629a67',
		
		/**
		 * 高德API
		 */
		
		'GD_YUN_KEY' =>'287b31926ea323a8794ec4bb385eac7c',
		//开发测试环境
		'GD_TABLE_ID_RD' =>'5a71562e305a2a284b1dcafb',
		//生产环境
		'GD_TABLE_ID_SERVER' =>'5a7167b27bbf1950757188ef',
		
		
		/**
		 * rabbitMQ
		 */
		
		'MQ_IP' => '121.40.181.182',
		'MQ_PORT' => '5672',
		'MQ_USER' => 'admin',
		'MQ_PWD' => 'LqWjMZXst1nrUEBU',
		'MQ_HOST' => '/',
		
		
	);
