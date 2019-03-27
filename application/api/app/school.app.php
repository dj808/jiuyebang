<?php
	
	/**
	 * Created by Malcolm.
	 * Date: 2018/4/17  20:32
	 */
	class SchoolApp extends ApiApp {
		public $userIc , $ic;
		
		public function __construct () {
			parent::__construct(true);
			
			
			$this->ic = ic('school');
			false && $this->ic = new School();
		}
		
		
		/**
		 * @todo    获取校园页面信息
		 * @author Malcolm  (2018年04月17日)
		 */
		public function getSchoolPageInfo(){
			$result = $this->ic->getSchoolPageInfo($this->req);
			$this->jsonReturn($result);
		}


        /**
         * @todo    获取趣事详情
         * @author Malcolm  (2018年05月29日)
         */
        public function getFunInfo(){
            $result = ic('fun')->getFunInfo($this->req,$this->userId);
            $this->jsonReturn($result);
        }


        /**
         * @todo    获取校园趣事列表
         * @author Malcolm  (2018年05月29日)
         */
        public function getFunList(){
            $result = ic('fun')->getFunList($this->req);
            $this->jsonReturn($result);
        }


        /**
         * @todo    获取【新】趣事列表
         * @author Zhulx  (2018年07月23日)
         */
        public function getDynamicList(){
            $result = ic('dynamic')->getList($this->req,$this->userId);
            $this->jsonReturn($result);
        }
	
        /**
         * @todo    获取【新】趣事详情
         * @author Zhulx  (2018年07月23日)
         */
        public function getDynamicInfo(){
            $result = ic('dynamic')->getDynamicInfo($this->req,$this->userId);
            $this->jsonReturn($result);
        }
        
        /**
         * @todo    获取话题列表
         * @author Zhulx  (2018年07月23日)
         */
        public function getTopicList(){
            $result = ic('dynamic')->getTopicList($this->req);
            $this->jsonReturn($result);
        }
        
        /**
         * @todo    获取话题详情
         * @author Zhulx  (2018年07月24日)
         */
        public function getTopicInfo(){
            $result = ic('dynamic')->getTopicInfo($this->req,$this->userId);
            $this->jsonReturn($result);
        }
        
	 /**
        * @todo    获取粉丝或关注列表
        * @author Zhulx  (2018年07月24日)
        */
        public function getFollow(){
            $result = ic('user')->getFollow($this->req,$this->userId);
            $this->jsonReturn($result);
        }
        
         /**
        * @todo    获取个人主页
        * @author Zhulx  (2018年07月24日)
        */
        public function getHomePage(){
            $result = ic('dynamic')->getHomePage($this->req,$this->userId);
            $this->jsonReturn($result);
        }
        
         /**
        * @todo   加好友查看更多
        * @author Zhulx  (2018年07月25日)
        */
        public function getFriendMore(){
            $this->needLogin();
            $result = ic('dynamic')->getFriendMore($this->req,$this->userId);
            $this->jsonReturn($result);
        }
        
        
        /**
        * @todo   加好友
        * @author Zhulx  (2018年07月26日)
        */
         public function getFriend(){
            $this->needLogin();
            $result = ic('dynamic')->getFriend($this->req,$this->userId);
            $this->jsonReturn($result);
        }
        
        /**
        * @todo   趣事增加分享数
        * @author Zhulx  (2018年07月26日)
        */
         public function addShareNum(){
            $result = ic('dynamic')->addShareNum($this->req);
            $this->jsonReturn($result);
        }
    }