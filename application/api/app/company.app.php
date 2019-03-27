<?php


/**
 *  公司控制器
 * Created by Malcolm.
 * Date: 2018/6/20  18:39
 */
class CompanyApp extends ApiApp {
    public $userMod , $companyMod,$companyIc,$companyId;

    public function __construct() {
        parent::__construct(true);
        $this->userMod = m('user');
        false && $this->userMod = new UserMod();

        $this->companyMod = m('company');
        false && $this->companyMod = new CompanyMod();

        $this->companyIc = ic('company');
        false && $this->companyIc = new Company();

        $this->needLogin();

        //获取公司ID
        $this->companyId = $this->companyMod->getCompanyIdByUserId($this->userId);
    }


    /**
     * @todo    获取公司信息
     * @author Malcolm  (2018年06月20日)
     */
    public function getCompanyInfo(){
        $result = $this->companyIc->getCompanyInfo($this->req,$this->userId,$this->companyId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    修改公司信息
     * @author Malcolm  (2018年06月20日)
     */
    public function editCompanyInfo(){
        $result = $this->companyIc->editCompanyInfo($this->req,$this->userId,$this->companyId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    获取我发布的职位
     * @author Malcolm  (2018年06月20日)
     */
    public function geyMyJobList(){
        $result = $this->companyIc->geyMyJobList($this->req,$this->userId,$this->companyId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    修改职位状态
     * @author Malcolm  (2018年06月20日)
     */
    public function setJobStatus(){
        $result = $this->companyIc->setJobStatus($this->req,$this->userId,$this->companyId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    获取我发布的培训
     * @author Malcolm  (2018年06月20日)
     */
    public function getMyTrainList(){
        $result = $this->companyIc->getMyTrainList($this->req,$this->userId,$this->companyId);
        $this->jsonReturn($result);
    }

    /**
     * @todo    修改培训状态
     * @author Malcolm  (2018年06月20日)
     */
    public function setTrainStatus(){
        $result = $this->companyIc->setTrainStatus($this->req,$this->userId,$this->companyId);
        $this->jsonReturn($result);
    }



}