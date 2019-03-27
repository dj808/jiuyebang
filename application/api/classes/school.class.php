<?php
	
	
	/**
	 * 校园控制器拓展
	 * Created by Malcolm.
	 * Date: 2018/4/17  20:44
	 */
	class School extends Zeus {
		public $mod;
		
		public  function __construct() {
			parent::__construct("user");
			
			$this->mod = m('user');
			false&&$this->mod = new UserMod();
		}
		
		
		/**
		 * @todo    获取校园页面信息
		 * @author Malcolm  (2018年04月17日)
		 */
		public function getSchoolPageInfo($param){
			//招聘列表
			$param['type'] = 1;
			
			$_REQUEST['perpage'] = 4;
			$jobList = ic('job')->getList($param,0,true);
			
			
			//每日分享列表
			$newsList = ic('news')->getNewsList([],true);

			//获取趣事列表
            $funList = ic('fun')->getFunList([],true);
			
			
			$data = [
				'job_list' => $jobList,
				'news_list' => $newsList,
				'fun_list' => $funList,
			];
			
			return message('操作成功', true ,$data);
		}



		
		
		
	}