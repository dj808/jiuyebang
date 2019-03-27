<?php


/**
 *  投票数据更新
 * Created by Malcolm.
 * Date: 2018/6/20  11:31
 */
class TestApp extends BaseApp{
	public $logoPath;
    public function __construct() {
        parent::__construct();

	    $this->logoPath = '/www/logs/opus.log';
    }



    public function index() {
        //先获取原有投票记录
        $cond = " user_id = 0 AND mark = 1 ";

        $mod = m('opus');
        false&&$mod = new OpusMod();

        $relMod = m('opus_vote_rel');

        $ids = $relMod->getIds($cond);

	    if (is_array($ids)) {
		    foreach ($ids as $key => $val) {
			    $info = $relMod->getInfo($val);


			    $mod->transStart();

			    $rs = $relMod->drop($info['id']);
			    if(!$rs){
			    	$mod->transBack();
			    	echo " {$info['id']} 删除错误  \n";
			    	continue;
			    }


			    if($info['opus_id']){
				    $opusInfo = $mod->getInfo($info['opus_id']);

				    $data = [
				    	'vote_num' =>  $opusInfo['vote_num'] - 1
				    ];

				    $rs = $mod->edit($data,$opusInfo['id']);

				    if(!$rs){
					    $mod->transBack();
					    echo " {$info['id']} 删除错误  \n";
					    continue;
				    }

			    }


			    $mod->transCommit();

			    echo " {$info['id']} 已经处理  \n";

            }
	    }


        echo "刷新成功  \n";
        return true;
    }


	/**
	 * @todo    添加假帐号
	 * @author Malcolm  (2018年07月09日)
	 */
    public function manageUser(){
	    for ($x=1; $x<=100; $x++) {

		    $num=str_pad($x,3,"0",STR_PAD_LEFT);
		    $mobile = "13000000{$num}";

		    $pwd = 'jyb891918';

			$data = [
				'mobile' =>$mobile,
				'password' =>md5($pwd),
				'device'=>3
			];


			$mod = m('user');

		    $mod->transStart();

		    $rs = $mod->edit($data);

		    if (!$rs) {
			    $mod->transBack();
			    echo " {$mobile} 写入错误   \n ";

			    continue;
		    }


		    //根据ID 生成邀请码
		    $selfCode = Zeus::getCodeById($rs);

		    $rss = $mod->edit([
			    'code' =>$selfCode,
			    'nickname' =>"jyb" . substr($mobile , 7),
		    ],$rs);

		    if (!$rss) {
			    $mod->transBack();
			    echo " {$mobile} 写入错误   \n ";

			    continue;
		    }

		    $mod->transCommit();

		    echo " {$mobile} 写入成功   \n ";
	    }


	    echo " 完成   \n ";
    }


	/**
	 * @todo    维护学校
	 * @author Malcolm  (2018年07月10日)
	 */
    public function manageSchool(){
		$cond = 'id > 0';

		$mod = m('college');

		$ids =  $mod->getIds($cond);

	    if (is_array($ids)) {
		    foreach ($ids as $key => $val) {
			    $info = $mod->getInfo($val);

			    $initial = Zeus::getFirstCharter($info['name']);

			    if($initial){
			    	$rs = $mod->edit([
			    		'initial' => $initial
				    ],$info['id']);


			    	if($rs)
					    echo " {$info['name']} 写入成功   \n ";
			    }


		    }
	    }

	    echo " 完成   \n ";
	    return true;
    }



	/**
	 * @todo    维护学校
	 * @author Malcolm  (2018年07月10日)
	 */
	public function manageMajor(){
		$cond = 'id > 0';

		$mod = m('major');

		$ids =  $mod->getIds($cond);

		if (is_array($ids)) {
			foreach ($ids as $key => $val) {
				$info = $mod->getInfo($val);

				$initial = Zeus::getFirstCharter($info['name']);

				if($initial){
					$rs = $mod->edit([
						'initial' => $initial
					],$info['id']);


					if($rs)
						echo " {$info['name']} 写入成功   \n ";
				}


			}
		}

		echo " 完成   \n ";
		return true;
	}


	/**
	 * @todo    维护用户统计
	 * @author Malcolm  (2018年07月14日)
	 */
	public function manageTongji(){
		$mod = m('job');

		$smod = m('statistics');

		$cond = " mark = 1 ";

		$ids = $mod->getIds($cond);

		if (is_array($ids)) {
			foreach ( $ids as $key => $val) {
				$info = $mod->getInfo($val);
				$day = date("Y-m-d", $info['add_time']);

				//维护数据
				$rs = $smod->manage(4,1,$day);

				echo " 完成  {$val}  \n ";
		    }
		}

		echo " 已完成    \n ";

		return true;
	}


	/**
	 * @todo    维护投票数
	 * @author Malcolm  (2018年07月19日)
	 */
	public function manageVote(){
		START_CONNECTION:

		$cond = " mark = 1 ";

		$mod = m('opus');

		$relMod = m('opus_vote_rel');

		$ids = $mod->getIds($cond);

		if (is_array($ids)) {
			foreach ($ids as $key => $val) {
				$tmp = " opus_id = {$val} AND user_id > 0 AND mark = 1 ";

				$num = $relMod->getCount($tmp);

				$rs = $mod->edit([
					'vote_num' => $num
				],$val);

		    }
		}

		echo " 完成  \n ";

		sleep(1);

		goto START_CONNECTION;

	}


	/**
	 * @todo    刷投票数据
	 * @author Malcolm  (2018年07月19日)
	 */
	public function vote(){
		START_CONNECTION:



		$log = " 开始刷票   ";

		$this->editLogFile($log,$this->logoPath);

		$mod = m('opus');
		false&&$mod = new OpusMod();
		$opusId = 7;

		//查询投票最多的
		$query = [
			'fields'   => 'id' ,
			'order_by' => 'vote_num DESC' ,
			'cond'     => " id <> {$opusId} AND mark = 1 ",
		];

		$data = $mod->getOne($query);

		$info = $mod->getInfo($data['id']);

		$chooseInfo = $mod->getInfo($opusId);

		//如果不是第一名
		if($chooseInfo['vote_num'] <= $info['vote_num'] || $chooseInfo['vote_num']-$info['vote_num'] <= rand(300,500) ){
			//随机取出一定的用户ID 去投票
			$rand = rand(50,92);

			//起始用户ID
			$s = 15212;

			$relMod = m('opus_vote_rel');

			$tmpInfo = $mod->getInfo($opusId);

			for ($x=1; $x<=$rand; $x++) {

				$now = time();
				if($now>=1532070000){
					$log = " 已超投票时间   ";

					$this->editLogFile($log,$this->logoPath);
					exit();
				}

				$mod->transStart();

				$data = [
					'flag' => $tmpInfo['flag']+1,
					'look_num' => $tmpInfo['look_num']+rand(1,9),
				];

				$rs = $mod->edit($data,$opusId);

				if(!$rs){
					$mod->transBack();

					$log = " 插入投票失败   ";

					$this->editLogFile($log,$this->logoPath);

					continue;
				}


				$rss = $relMod->edit([
					'opus_id' => $opusId,
					'user_id' => $s,
					'flag' => 1,
				]);


				if(!$rss){
					$mod->transBack();
					$log = " 插入投票失败   ";

					$this->editLogFile($log,$this->logoPath);

					continue;
				}


				$mod->transCommit();
				$log = " 成功投票 +1   ";

				$this->editLogFile($log,$this->logoPath);

				$s++;

				$tms = rand(1,3);

				$log = " 休息{$tms}秒   ";

				$this->editLogFile($log,$this->logoPath);

				sleep($tms);
			}


			$m = intval(rand(30,60)/60) ;

			$log = " 已经成功刷入 {$rand} 票 ， 休息 {$m} 分钟后继续   ";

			$this->editLogFile($log,$this->logoPath);

			sleep($m*60);
			goto START_CONNECTION;
		}else{
			$log = " 已经是第一名，无需刷票   ";

			$this->editLogFile($log,$this->logoPath);


			$m = intval(rand(30,60)/60) ;
			sleep($m*60);
			goto START_CONNECTION;
		}




	}




	public function getUserList(){
		$query = [
			'fields'   => 'id,user_id' ,
			'order_by' => 'add_date DESC' ,
			'cond'     => " opus_id = 75 AND user_id > 0 AND mark = 1 ",
			'limit'     => 50,
		];

		$data = m('opus_vote_rel')->getData($query);

		$userMod = m('user');

		$uMod = m('resume');


		if (is_array($data)) {
			foreach ($data as $key => $val) {
				$userInfo =$userMod->getInfo($val['user_id']);

				$uInfo =$uMod->getInfoByUserId($val['user_id']);

					echo " 姓名： {$uInfo['name']} , 昵称：{$userInfo['nickname']} , 电话： {$userInfo['mobile']}  \n";

		    }
		}


		return true;
	}




	public function manage(){
		$cond = " opus_id = 7 AND user_id > 0 AND mark = 1 ";

		$query = [
			'fields'   => 'id,user_id' ,
			'order_by' => 'add_date DESC' ,
			'cond'     => $cond,
			'limit'     => 2780,
		];

		$mod = m('opus_vote_rel');

		$data = $mod->getData($query);

		if (is_array($data)) {
			foreach ($data as $key => $val) {
				$mod->drop($val['id']);
		    }
		}



		echo " 处理完成  \n ";

		return true;
	}


	/**
	 * @todo    维护职位
	 * @author Malcolm  (2018年08月02日)
	 */
	public function manageJob(){
		$mod = m('job_industry');

		//取出所有的类型
		$tmp = Zeus::config('job_industry');
		if (is_array($tmp)) {
			foreach ($tmp as $key => $val) {
				$ra = $mod->edit([
					'id' => $key+6,
					'name' => $val,
				]);
		    }
		}

		echo '完成';

	}



}