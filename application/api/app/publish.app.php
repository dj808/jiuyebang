<?php

/**
 *  发布控制器
 * Created by Malcolm.
 * Date: 2018/6/12  10:01
 */
class PublishApp extends ApiApp {
    public $userIc , $ic;

    public function __construct() {
        parent::__construct(true);
        $this->userIc = ic('user');
        false && $this->userIc = new User();

        $this->ic = ic('publish');
        false && $this->ic = new Publish();

        $this->needLogin();
    }


    /**
     * @todo    获取职位选项列表
     * @author Malcolm  (2018年06月12日)
     */
    public function getJobOptionList(){
        $result = $this->ic->getJobOptionList();
        $this->jsonReturn($result);
    }

    /**
     * @todo    发布职位
     * @author Malcolm  (2018年06月12日)
     */
    public function addJob(){
        $result = $this->ic->addJob($this->req,$this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    发布培训时，获取选项列表
     * @author Malcolm  (2018年06月14日)
     */
    public function getTrainOptionList(){
        $result = $this->ic->getTrainOptionList();
        $this->jsonReturn($result);
    }

    /**
     * @todo    发布培训
     * @author Malcolm  (2018年06月14日)
     */
    public function addTrain(){
        $result = $this->ic->addTrain($this->req,$this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    发布趣事
     * @author Malcolm  (2018年06月20日)
     */
    public function pushDynamic(){
        $dynamicIc = ic('dynamic');

        $result = $dynamicIc->pushDynamic($this->req,$this->userId);
        $this->jsonReturn($result);
    }




}