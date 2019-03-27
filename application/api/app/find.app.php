<?php
	
	/**
	 * 发现控制器
	 * Created by Malcolm.
	 * Date: 2018/4/17  10:54
	 */
	class FindApp extends ApiApp {
		public $userIc,$ic;
		
		public function __construct() {
			parent::__construct(true);
			
			$this->ic = ic('find');
			false && $this->ic = new Find();
		}
		
		
		/**
		 * @todo    获取每日分享分类列表
		 * @author Malcolm  (2018年04月17日)
		 */
		public function getNewsCateList(){
			$result = $this->ic->getNewsCateList();
			$this->jsonReturn($result);
		}
		
		
		/**
		 * @todo    获取每日分享列表
		 * @author Malcolm  (2018年04月17日)
		 */
		public function getNewsList(){
			$newsIc = ic('news');
			$result = $newsIc->getNewsList($this->req);
			
			$this->jsonReturn($result);
		}
		
		
		/**
		 * @todo    获取每日分享详情
		 * @author Malcolm  (2018年04月17日)
		 */
		public function getNewsInfo(){
			$newsIc = ic('news');
			$result = $newsIc->getNewsInfo($this->req,$this->userId);
			
			$this->jsonReturn($result);
		}
		
		/**
		 * @todo    获取就业邦学堂页面信息
		 * @author Malcolm  (2018年04月17日)
		 */
		public function getSchoolPageInfo(){
			$result = $this->ic->getSchoolPageInfo($this->req,$this->userId);
			$this->jsonReturn($result);
		}
		
		
		/**
		 * @todo    获取 攻略/秘籍/试卷 筛选选项
		 * @author Malcolm  (2018年04月17日)
		 */
		public function getRaidersOptionList(){
			$result = ic('raiders')->getRaidersOptionList($this->req);
			$this->jsonReturn($result);
		}
		
		
		/**
		 * @todo    获取攻略/秘籍/试卷列表
		 * @author Malcolm  (2018年04月17日)
		 */
		public function getRaidersList(){
			$result = ic('raiders')->getList($this->req,$this->userId);
			$this->jsonReturn($result);
		}
		
		
		/**
		 * @todo    获取 攻略/秘籍/试卷 详情
		 * @author Malcolm  (2018年04月17日)
		 */
		public function getRaidersInfo(){
			$result = ic('raiders')->getRaidersInfo($this->req,$this->userId);
			$this->jsonReturn($result);
		}
		
		
		/**
		 * @todo    获取附近的人
		 * @author Malcolm  (2018年04月17日)
		 */
		public function getNearbyPeople(){
			$result = $this->ic->getNearbyPeople($this->req,$this->userId);
			$this->jsonReturn($result);
		}
		
		
		
	}