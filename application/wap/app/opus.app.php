<?php

/**
 *  投票控制器
 * Created by Malcolm.
 * Date: 2018/5/19  12:31
 */
class OpusApp extends FrontendApp{
    public $userId,$voteDate,$mod,$relMod,$voteStatus,$canVote;

    public function __construct() {
        parent::__construct();

        $this->userId = $this->params['user_id'];

        $this->voteDate = Zeus::config('vote_time');

        $this->voteDate = explode(',',$this->voteDate);

        $this->mod = m('opus');
        false&&$this->mod = new OpusMod();

        $this->relMod = m('opus_vote_rel');

        //是否已经投票 1是2否
        $this->voteStatus = 2;

        //是否可以投票 1是2否
        $this->canVote = 2;

        $this->checkNum();

        $this->isVote(true);

        $this->isInDate();

        $this->assign('user_id',$this->userId);

        $this->getTotalOpus();
    }


    /**
     * @todo    首页
     * @author Malcolm  (2018年06月21日)
     */
    public function index() {
        //不可投票
        if($this->voteStatus==1 || $this->canVote==2){
            $this->error();
        }


        $start = date('Y/m/d H:i',$this->voteDate[0]);
        $end = date('Y/m/d H:i',$this->voteDate[1]);

        $text = "投票时间：{$start} -- {$end}";

        $list = $this->mod->getList($this->params['keyword']);

        $this->assign('keyword',$this->params['keyword']);

        $this->assign('list',$list);

        $this->assign('note',$text);

        $this->display('opus/index.html');
    }


    /**
     * @todo    详情页面
     * @author Malcolm  (2018年06月22日)
     */
    public function detail(){
        $id = $this->params['id'];

	    $this->isVote(false,$id);

        if(!$id)
            $this->error();

        $info = $this->mod->getInfo($id);

        //自动维护查看次数
        $this->mod->editColumnValue($id,'look_num');


        $start = date('m/d H:i',$this->voteDate[0]);
        $end = date('m/d H:i',$this->voteDate[1]);

        if($this->voteStatus==1){
            $text = '您已投票，无法重复投票';
            $text2 = '感谢你的参与，你的支持弥足珍贵';
        }else{
            $text = "投票时间：{$start} -- {$end}";
            $text2 = '暂时不在投票时间范围内，无法投票';
        }

        $this->assign('note',$text);
        $this->assign('note1',$text2);

        $this->assign('info',$info);

        $this->display('opus/detail.html');
    }


    /**
     * @todo    禁止投票页面
     * @author Malcolm  (2018年06月22日)
     */
    public function error(){
	    $this->isVote(true);

        $start = date('m/d H:i',$this->voteDate[0]);
        $end = date('m/d H:i',$this->voteDate[1]);

        if($this->voteStatus==1){
            $text = '您已投票10次，无法投票';
            $text2 = '感谢你的参与，你的支持弥足珍贵';
        }else{
            $text = "投票时间：{$start} -- {$end}";
            $text2 = '暂时不在投票时间范围内，无法投票';
        }

        $list = $this->mod->getEndList();

        $this->assign('note',$text);
        $this->assign('note1',$text2);

        $this->assign('list',$list);

        $this->display('opus/error.html');
        exit();
    }


    /**
     * @todo    投票
     * @author Malcolm  (2018年06月23日)
     */
    public function vote(){
    	if(!$this->params['id'] || !$this->params['user_id'])
		    $this->ajaxReturn(R('参数错误',false));

    	$userInfo = m('user')->getInfo($this->params['user_id']);
    	if(!$userInfo['add_time'])
		    $this->ajaxReturn(R('参数错误',false));

    	$resumeInfo = m('resume')->getInfoByUserId($this->params['user_id']);
    	if(!$resumeInfo['name'])
		    $this->ajaxReturn(R('请完善简历后继续投票',false));


	    $this->isVote(false,$this->params['id']);
        if ($this->voteStatus==1)
            $this->ajaxReturn(R('您已投票，无法重复投票',false));

	    $this->isVote(true);
	    if ($this->voteStatus==1)
		    $this->ajaxReturn(R('您已投票10次，无法继续投票',false));

        if ($this->canVote==2)
            $this->ajaxReturn(R('当前不在投票期限内，无法投票',false));

        $info = $this->mod->getInfo($this->params['id']);

        if(!$info['id'])
            $this->ajaxReturn(R('参数错误',false));



        $this->mod->transStart();
	    /*$rs = $this->mod->editColumnValue($info['id'],'vote_num');
        //$rs = $this->mod->edit($data,$info['id']);
        if(!$rs){
            $this->mod->transBack();
            $this->ajaxReturn(R('系统繁忙，请稍候再试',false));
        }*/

        //维护投票详情
        $relData = [
            'opus_id' => $this->params['id'],
            'user_id' => $this->params['user_id'],
            'add_date' => date('Y-m-d H:i:s',time())
        ];


        $rs = $this->relMod->edit($relData);

        if(!$rs){
            $this->mod->transBack();
            $this->ajaxReturn(R('系统繁忙，请稍候再试',false));
        }

        $this->mod->transCommit();

        //发送成功推送
        Zeus::sendMsg([
            'type' =>['msg','push'],
            'user_id' =>$this->params['user_id'],
            'title' =>'投票成功通知',
            'content' =>'您已成功投票，感谢您的参与，活动结束后，我们会按参与顺序发放大礼包！',
            'msg_type' =>1,
            'user_type' =>1,
        ]);


        $this->ajaxReturn(R('投票成功',true));
    }


    /**
     * @todo    获取分享详情
     * @author Malcolm  (2018年06月23日)
     */
    public function share(){
        $id = $this->params['id'];
        if(!$id)
            $this->error();

        $info = $this->mod->getInfo($id);

        //自动维护查看次数
        $this->mod->editColumnValue($id,'look_num');

        $this->assign('info',$info);

        $this->display('opus/share.html');
    }




    /**
     * @todo    判断在不在投票时间范围内
     * @author Malcolm  (2018年06月21日)
     */
    public function isInDate(){
        $time = time();

        if($time<$this->voteDate[0])
            $this->canVote = 2;

        else if($time>=$this->voteDate[1])
            $this->canVote = 2;

        else
            $this->canVote = 1;

        $this->assign('canVote',$this->canVote);
    }


    /**
     * @todo    是否已经投票
     * @author Malcolm  (2018年06月21日)
     */
    public function isVote($isTotal=false,$id=0){
    	if(!$isTotal){
    		if($id)
			    $cond = " user_id = {$this->userId} AND opus_id = {$id} AND mark = 1 ";
    		else
			    $cond = " user_id = {$this->userId} AND mark = 1 ";
	    }
    	else
		    $cond = " user_id = {$this->userId}  AND mark = 1 ";

        $ids = $this->relMod->getIds($cond);

        if(!$isTotal){
	        if(count($ids))
		        $this->voteStatus = 1;
	        else
		        $this->voteStatus = 2;
        }else{
	        if(count($ids)>=10)
		        $this->voteStatus = 1;
	        else
		        $this->voteStatus = 2;
        }




        $this->assign('voteStatus',$this->voteStatus);
    }


    /**
     * @todo    查询投票数
     * @author Malcolm  (2018年06月21日)
     */
    public function checkNum(){
        $sql = " SELECT SUM(look_num) as look_num , SUM(vote_num) as vote_num FROM njyb_opus WHERE mark = 1 ";

        $rs = $this->mod->getOneBySql($sql);

        //投票
	    $sql = " SELECT count(id) as counts FROM njyb_opus_vote_rel WHERE user_id > 0 AND mark = 1 ";

	    $rss = $this->relMod->getOneBySql($sql);

	    $rs['vote_num'] = $rss['counts'];


        $this->assign('num',$rs);
    }


	/**
	 * @todo    获取总项目数
	 * @author Malcolm  (2018年07月08日)
	 */
    public function getTotalOpus(){
		$cond = " mark = 1 ";

		$num = $this->mod->getCount($cond);

	    $this->assign('totalOpusNum',$num);

		return $num;
    }



}