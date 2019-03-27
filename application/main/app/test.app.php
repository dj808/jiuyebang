<?php
	
	/**
	 * 测试方法 ，生产环境删除
	 * Created by Malcolm.
	 * Date: 2018/1/31  10:45
	 */
	class TestApp extends BackendApp {
		public function __construct () {
			parent::__construct();
		}
		
		
		
		public function index(){

			$rs = Hera::checkSMSCode('17625915422','9999');
			printd($rs);
			
			
		}
		
		public function upload(){
			$rs = Hera::upload('test');
			printd($rs);
			//printd($_FILES);
		}
		
	}