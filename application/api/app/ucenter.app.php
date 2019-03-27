<?php

/**
 * 个人中心入口
 * Created by Malcolm.
 * Date: 2018/2/2  14:57
 */
class UcenterApp extends ApiApp {
    public $userIc , $ic;

    public function __construct() {
        parent::__construct(true);
        $this->userIc = ic('user');
        false && $this->userIc = new User();

        $this->ic = ic('ucenter');
        false && $this->ic = new Ucenter();

        $this->needLogin();
    }


    /**
     * @todo    编辑基本资料
     * @author  Malcolm  (2018年02月02日)
     */
    public function editBaseInfo() {
        $result = $this->userIc->editBase($this->req , $this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    修改密码
     * @author  Malcolm  (2018年02月02日)
     */
    public function editPsw() {
        $result = $this->userIc->resetPwdByUser($this->req , $this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    获取我的实名认证信息
     * @author  Malcolm  (2018年02月02日)
     */
    public function getAuthentication() {
        $result = $this->ic->getAuthentication($this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    提交实名认证
     * @author  Malcolm  (2018年02月02日)
     */
    public function setAuthentication() {
        $result = $this->ic->setAuthentication($this->req , $this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    获取我的简历信息
     * @author  Malcolm  (2018年02月02日)
     */
    public function getMyResumeInfo() {
        $result = $this->ic->getMyResumeInfo($this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    编辑简历时，获取大学列表
     * @author  Malcolm  (2018年02月05日)
     */
    public function getCollegeList() {
        $result = $this->ic->getCollegeList($this->params['keyword']);
        $this->jsonReturn($result);
    }


    /**
     * @todo    编辑简历时，获取专业列表
     * @author  Malcolm  (2018年02月05日)
     */
    public function getMajorList() {
        $result = $this->ic->getMajorList($this->req);
        $this->jsonReturn($result);
    }


    /**
     * @todo    编辑简历时，获取学历列表
     * @author  Malcolm  (2018年02月05日)
     */
    public function getEduList() {
        $result = $this->ic->getEduList();
        $this->jsonReturn($result);
    }


    /**
     * @todo    编辑简历时，获取工作时间列表
     * @author  Malcolm  (2018年02月05日)
     */
    public function getJobTimeList() {
        $result = $this->ic->getJobTimeList();
        $this->jsonReturn($result);
    }

    /**
     * @todo    编辑简历时，获取工作类型列表
     * @author  Malcolm  (2018年02月05日)
     */
    public function getJobTypeList() {
        $result = $this->ic->getJobTypeList();
        $this->jsonReturn($result);
    }

    /**
     * @todo    编辑简历
     * @author  Malcolm  (2018年02月05日)
     */
    public function setMyResumeInfo() {
        $result = $this->ic->setMyResumeInfo($this->req , $this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    我的优惠券
     * @author  Malcolm  (2018年02月05日)
     */
    public function getMyCoupon() {
        $result = $this->ic->getMyCoupon($this->req , $this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    我的消息
     * @author  Malcolm  (2018年02月06日)
     */
    public function getMessage() {
        $result = $this->ic->getMessage($this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    设置消息阅读状态
     * @author  wangqs  (2017年04月19日)
     */
    public function setMessageRead() {
        $result = $this->ic->setMessageRead($this->req , $this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    获取我的发布
     * @author  Malcolm  (2018年02月07日)
     */
    public function getMyPush() {
        $result = $this->ic->getMyPush($this->req , $this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    添加发布
     * @author  Malcolm  (2018年03月07日)
     */
    public function setPush() {
        $result = $this->ic->setPush($this->req , $this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    互助确认完成时，获取已申请列表
     * @author  Malcolm  (2018年03月09日)
     */
    public function getCooperChooseList() {
        $result = $this->ic->getCooperChooseList($this->req , $this->userId);
        $this->jsonReturn($result);
    }

    /**
     * @todo    确认完成互助
     * @author  Malcolm  (2018年03月09日)
     */
    public function setPushFinish() {
        $result = $this->ic->setPushFinish($this->req , $this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    获取我的收藏
     * @author  Malcolm  (2018年04月10日)
     */
    public function getMyCollect() {
        $result = $this->ic->getMyCollect($this->req , $this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    删除我的收藏
     * @author  Malcolm  (2018年04月19日)
     */
    public function dropMyCollect() {
        $result = $this->ic->dropMyCollect($this->req , $this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    根据月份获取签到列表
     * @author  Malcolm  (2018年04月11日)
     */
    public function getSignDays() {
        $month = trim($this->req['month']);
        $signRecordMod = m("signRecord");
        false && $signRecordMod = new SignRecordMod();

        //月份记录
        $data['list'] = $signRecordMod->getDayList($month , $this->userId);

        //获取最后一次签到记录
        $lastInfo = $signRecordMod->getLastSignInfo($this->userId);

        $data['total_num'] = $lastInfo['total_num'];
        $data['continuity_num'] = $lastInfo['continuity_num'];

        $data['growth_sign'] = $this->userInfo['growth_sign'];


        $data['is_signed'] = date("Y-m-d") == $lastInfo['sign_date'] ? 1 : 2;


        $this->jsonReturn(message('操作成功' , true , $data));
    }


    /**
     * @todo    签到
     * @author  Malcolm  (2018年04月11日)
     */
    public function sign() {
        $result = $this->ic->sign($this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    获取我的兼职成长记录
     * @author  Malcolm  (2018年04月11日)
     */
    public function myJobList() {
        $result = $this->ic->myJobList($this->req , $this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    获取我的培训成长记录
     * @author  Malcolm  (2018年04月11日)
     */
    public function myTrainingList() {
        $result = $this->ic->myTrainingList($this->req , $this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    获取我的互助成长记录
     * @author  Malcolm  (2018年04月11日)
     */
    public function myHelpList() {
        $result = $this->ic->myHelpList($this->req , $this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    设置求职意向时，获取选择项
     * @author  Malcolm  (2018年04月11日)
     */
    public function getJobIntentionOption() {
        $result = $this->ic->getJobIntentionOption($this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    设置求职意向
     * @author  Malcolm  (2018年04月11日)
     */
    public function setJobIntentionOption() {
        $result = $this->ic->setJobIntentionOption($this->req , $this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    获取我的职位申请列表
     * @author  Malcolm  (2018年04月11日)
     */
    public function getMyJobApplyList() {
        $result = $this->ic->getMyJobApplyList($this->req , $this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    我的任务
     * @author  Malcolm  (2018年04月12日)
     */
    public function getMyTaskInfo() {
        $result = $this->ic->getMyTaskInfo($this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    获取可领取的兼职任务列表
     * @author  Malcolm  (2018年04月12日)
     */
    public function getJobTaskList() {
        $result = $this->ic->getJobTaskList($this->req , $this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    领取任务
     * @author  Malcolm  (2018年04月12日)
     */
    public function setNewTask() {
        $result = $this->ic->setNewTask($this->req , $this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    获取我的兼职任务列表
     * @author  Malcolm  (2018年04月12日)
     */
    public function getMyJobTaskList() {
        $result = $this->ic->getMyJobTaskList($this->req , $this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    设置兼职任务完成
     * @author  Malcolm  (2018年04月12日)
     */
    public function setJobTaskFinish() {
        $result = $this->ic->setJobTaskFinish($this->req , $this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    获取个人资料
     * @author  Malcolm  (2018年04月17日)
     */
    public function getMyInfo() {
        $result = $this->ic->getMyInfo($this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    资料是否完善
     * @author Malcolm  (2018年05月26日)
     */
    public function isCompleteInfo() {
        $result = $this->ic->isCompleteInfo($this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    投票前获取简单资料
     * @author Malcolm  (2018年05月26日)
     */
    public function getSimpleData(){
        $result = $this->ic->getSimpleData($this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    投票前完善简单资料
     * @author Malcolm  (2018年05月26日)
     */
    public function setSimpleData(){
        $result = $this->ic->setSimpleData($this->req,$this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    获取用户类型
     * @author Malcolm  (2018年06月13日)
     */
    public function getUserType(){
        $result = $this->userIc->getUserType($this->userId);
        $this->jsonReturn($result);
    }


    /**
     * @todo    获取我发布的职位信息列表
     * @author Malcolm  (2018年06月14日)
     */
    public function getMyPushJobList(){
        $result = $this->ic->getMyPushJobList($this->req,$this->userId);
        $this->jsonReturn($result);
    }

    /**
     * @todo    添加关注
     * @author Zhulx  (2018年07月16日)
     */
    public function addFollow(){
        $result = $this->userIc->addFollow($this->req,$this->userId);
        $this->jsonReturn($result);
    }
    

}