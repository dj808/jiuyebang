<?php
	
	
	/**
	 * 分享控制器
	 * Created by Malcolm.
	 * Date: 2018/4/27  19:51
	 */
	class ShareApp extends FrontendApp{
		public $id;
		
		public function __construct() {
			parent::__construct();
			
			$this->id = $this->params['id'];
		}
		
		
		/**
		 * @todo    发现
		 * @author Malcolm  (2018年04月27日)
		 */
		public function raiders(){
			$info = m('raiders')->getDetailInfo($this->id);
			
			$this->assign('info',$info);
			
			//tdk
			$this->TDK['t'] = $info['raiders_title'];
			
			$this->assign('TDK',$this->TDK);
			
			$this->render('raiders.html');
		}
		
		
		/**
		 * @todo    每日分享
		 * @author Malcolm  (2018年04月27日)
		 */
		public function news(){
			$info = m('news')->getDetailInfo($this->id);
			
			$this->assign('info',$info);
			
			//tdk
			$this->TDK['t'] = $info['news_title'];
			
			$this->assign('TDK',$this->TDK);
			
			$this->render('news.html');
		}
		
		
		/**
		 * @todo    职位
		 * @author Malcolm  (2018年04月27日)
		 */
		public function job(){
			$info = m('job')->getDetailInfo($this->id);
			$this->assign('info',$info);
			
			//tdk
			$this->TDK['t'] = $info['job_title'];
			
			$this->assign('TDK',$this->TDK);
			
			if($info['job_type'] == 1)
				$this->render('job1.html');
			else
				$this->render('job2.html');
		}
		
		
		/**
		 * @todo    培训
		 * @author Malcolm  (2018年04月27日)
		 */
		public function train(){
			$info = m('training')->getDetailInfo($this->id);
			
			$this->assign('info',$info);
			
			//tdk
			$this->TDK['t'] = $info['train_title'];
			
			$this->assign('TDK',$this->TDK);
			
			$this->render('train.html');
		}
                
                /**
		 * @todo    校内圈
		 * @author Zhulx  (2018年07月31日)
		 */
		public function dynamic(){
			$info = m('dynamic')->getListInfo($this->id);
			$comments = m('comment')->getData(['fields' => 'id','cond' => "state <> 2 AND type = 7 AND type_id = {$this->id} AND parent_id = 0 AND mark = 1",'limit' => '0,5']);
                        $newLists = [];
                        if ( is_array($comments) ) {
                            foreach ( $comments as $val ) {
                                $newLists[] = m('comment')->getDetailInfo($val['id']);
                            }
                        }
			$this->assign('info',$info);
			$this->assign('comments',$newLists);
			$this->render('dynamic.html');
		}


        /**
         * @todo    测试调用APP  JS
         * @author Malcolm  (2018年06月23日)
         */
		public function test(){
            $this->assign('TDK',$this->TDK);
            $this->render('test.html');
        }
		
	}