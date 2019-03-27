<?php
	
	/**
	 * 首页控制器
	 * Created by Malcolm.
	 * Date: 2018/4/12  19:35
	 */
	class HomeApp extends ApiApp {
		public $userIc,$ic,$jobIc,$cooperationIc,$trainIc;
		
		public function __construct() {
			parent::__construct(true);
			$this->userIc = ic('user');
			false && $this->userIc = new User();
			
			$this->ic = ic('home');
			false && $this->ic = new Home();
			
			$this->jobIc = ic('job');
			false && $this->jobIc = new Job();
			
			$this->cooperationIc = ic('cooperation');
			false && $this->cooperationIc = new Cooperation();
			
			$this->trainIc = ic('train');
			false && $this->trainIc = new Train();
			
		}
		
		/**
		 * @todo    根据区县级adcode获取市级adcode
		 * @author Malcolm  (2018年05月08日)
		 */
		public function getParentAdCode(){
			$code = $this->params['adcode'];
			$cond = " adcode = '{$code}' AND mark = 1 ";
			
			$mod =  m('city');
			$id = $mod->getIds($cond);
			
			if($id[0]){
				$info = $mod->getInfo($id[0]);
				$this->jsonReturn(message('操作成功', true ,[
					'adcode' => $info['p_adcode']
				]));
			}else{
				$this->jsonReturn(message('操作成功', true ,[
					'adcode' => 320100
				]));
			}
			
			
		}
		
		/**
		 * @todo    获取开放城市
		 * @author Malcolm  (2018年04月12日)
		 */
		public function getOpenCityList () {
			$result = $this->ic->getOpenCityList($this->req);
			$this->jsonReturn($result);
		}
		
		
		/**
		 * @todo    获取首页信息
		 * @author Malcolm  (2018年04月12日)
		 */
		public function getHomeInfo(){
			$result = $this->ic->getHomeInfo($this->req,$this->userId);
			$this->jsonReturn($result);
		}
		
		/**
		 * @todo    获取行业更多
		 * @author Zhulx (2018年08月03日)
		 */
		public function getIndustry(){
			$result = $this->ic->getIndustry();
			$this->jsonReturn($result);
		}
		
                
		/**
		 * @todo    获取职位列表的搜索条件
		 * @author Malcolm  (2018年04月13日)
		 */
		public function getConditionForJob(){
			$result = $this->jobIc->getConditionForJob($this->req);
			$this->jsonReturn($result);
		}
		
		/**
		 * @todo    获取职位列表
		 * @author Malcolm  (2018年04月13日)
		 */
		public function getJobList(){
			$result = $this->jobIc->getList($this->req,$this->userId);
			$this->jsonReturn($result);
		}
		
		
		/**
		 * @todo    获取职位详情
		 * @author Malcolm  (2018年04月13日)
		 */
		public function getJobInfo(){
			$result = $this->jobIc->getJobInfo($this->req,$this->userId);
			$this->jsonReturn($result);
		}
		
		
		/**
		 * @todo    添加收藏
		 * @author Malcolm  (2018年04月13日)
		 */
		public function setCollect(){
			$this->needLogin();
			$result = $this->ic->setCollect($this->req,$this->userId);
			$this->jsonReturn($result);
		}
		
		
		/**
		 * @todo    投递简历
		 * @author Malcolm  (2018年04月14日)
		 */
		public function setJobApply(){
			$this->needLogin();
			$result = $this->jobIc->setJobApply($this->req,$this->userId);
			$this->jsonReturn($result);
		}
		
		
		/**
		 * @todo    获取企业信息
		 * @author Malcolm  (2018年04月14日)
		 */
		public function getCompanyInfo(){
			$result = $this->jobIc->getCompanyInfo($this->req,$this->userId);
			$this->jsonReturn($result);
		}
		
		
		/**
		 * @todo    获取互助页面信息
		 * @author Malcolm  (2018年04月14日)
		 */
		public function getCooperationPageInfo(){
			$result = $this->ic->getCooperationPageInfo($this->req,$this->userId);
			$this->jsonReturn($result);
		}
		
		
		/**
		 * @todo   获取转让、互助列表
		 * @author Malcolm  (2018年04月14日)
		 */
		public function getCooperationList(){
			$result = $this->cooperationIc->getList($this->req,$this->userId);
			$this->jsonReturn($result);
		}
		
		
		/**
		 * @todo    获取转让、互助详情
		 * @author Malcolm  (2018年04月14日)
		 */
		public function getCooperationInfo(){
			$result = $this->cooperationIc->getCooperationInfo($this->req,$this->userId);
			$this->jsonReturn($result);
		}
		
		
		/**
		 * @todo    获取评论列表
		 * @author Malcolm  (2018年04月14日)
		 */
		public function getCommentList(){
			$commentIc = ic('comment');
			
			$result = $commentIc->getList($this->req,$this->userId);
			$this->jsonReturn($result);
		}
		
		
		/**
		 * @todo    申请互助/转让
		 * @author Malcolm  (2018年04月14日)
		 */
		public function serApplyCooperation(){
			$this->needLogin();
			$result = $this->cooperationIc->serApplyCooperation($this->req,$this->userId);
			$this->jsonReturn($result);
		}
		
		
		/**
		 * @todo    发布留言
		 * @author Malcolm  (2018年04月14日)
		 */
		public function setComment(){
                        $this->needLogin();
			$commentIc = ic('comment');
			$result = $commentIc->setComment($this->req,$this->userId);
			$this->jsonReturn($result);
		}
		
		
		/**
		 * @todo    获取培训页面搜索条件
		 * @author Malcolm  (2018年04月16日)
		 */
		public function getConditionForTrain(){
			$result = $this->trainIc->getConditionForTrain($this->req);
			$this->jsonReturn($result);
		}
		
		
		/**
		 * @todo    获取培训列表
		 * @author Malcolm  (2018年04月16日)
		 */
		public function getTrainList(){
			$result = $this->trainIc->getList($this->req,$this->userId);
			$this->jsonReturn($result);
		}
		
		
		/**
		 * @todo    获取培训详情
		 * @author Malcolm  (2018年04月16日)
		 */
		public function getTrainInfo(){
			$result = $this->trainIc->getTrainInfo($this->req,$this->userId);
			$this->jsonReturn($result);
		}
		
		
		/**
		 * @todo    企业入住申请
		 * @author Malcolm  (2018年04月16日)
		 */
		public function setCompanyApply(){
			$this->needLogin();
			$result = $this->ic->setCompanyApply($this->req,$this->userId);
			$this->jsonReturn($result);
		}
		
		
		/**
		 * @todo    获取搜索结果
		 * @author Malcolm  (2018年04月16日)
		 */
		public function getSearchInfo(){
			$result = $this->ic->getSearchInfo($this->req,$this->userId);
			$this->jsonReturn($result);
		}
		
		
		/**
		 * @todo    获取搜索关键词
		 * @author Malcolm  (2018年04月16日)
		 */
		public function getSearchPageKeyWords(){
			$result = $this->ic->getSearchPageKeyWords();
			$this->jsonReturn($result);
		}
		
		
		/**
		 * @todo    获取推荐列表
		 * @author Malcolm  (2018年04月17日)
		 */
		public function getRecommendList(){
			$result = $this->ic->getRecommendList($this->req,$this->userId);
			$this->jsonReturn($result);
		}


        /**
         * @todo    新增培训订单
         * @author Malcolm  (2018年06月14日)
         */
		public function setNewTrainOrder(){
            $this->needLogin();
            $result = $this->ic->setNewTrainOrder($this->req,$this->userId);
            $this->jsonReturn($result);
        }


        /**
         * @todo    新增点赞
         * @author Malcolm  (2018年06月20日)
         */
        public function setNewPraise(){
            $this->needLogin();
            $result = $this->ic->setNewPraise($this->req,$this->userId);
            $this->jsonReturn($result);
        }
		
	}