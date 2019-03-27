<?php

/**
 *  统计入库
 * Created by Malcolm.
 * Date: 2018/7/14  14:08
 */
class StatisticsApp extends BaseApp{
	public $mod,$userMod,$newsMod,$dynamicMod,$jobMod;
	public function __construct() {
		parent::__construct();

		$this->mod = m('statistics');
		false&&$this->mod = new StatisticsMod();

		$this->userMod = m('user');
		false&&$this->userMod = new UserMod();

		$this->newsMod = m('news');
		false&&$this->newsMod = new NewsMod();

		$this->dynamicMod = m('dynamic');
		false&&$this->dynamicMod = new DynamicMod();

		$this->jobMod = m('job');
		false&&$this->jobMod = new JobMod();

	}


	public function index() {
		$t = time();

		$date = date('Y-m-d',$t);

		$begin = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
		$endTime = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));


		$cond = " add_time >= {$begin} AND add_time <={$endTime} AND mark = 1 ";


		//处理用户
		$userIds = $this->userMod->getCount($cond);
		$this->mod->manage(1,$userIds,$date);



		echo " 用户处理完毕 \n ";


		//处理今日分享
		$newsIds = $this->newsMod->getCount($cond);
		$this->mod->manage(2,$newsIds,$date);

		echo " 今日分享处理完毕 \n ";


		//处理校园圈
		$dynamicIds = $this->dynamicMod->getCount($cond);

		$this->mod->manage(3,$dynamicIds,$date);

		echo " 校园圈处理完毕 \n ";


		//处理职位
		$jobIds = $this->jobMod->getCount($cond);

		$this->mod->manage(4,$jobIds,$date);

		echo " 用户处理完毕 \n ";


		return true;
	}


}