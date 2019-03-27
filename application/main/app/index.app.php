<?php

class IndexApp extends BackendApp {
	public $mod;

	public function __construct() {

		parent::__construct();
		$this->mod = m('user');
		false && $this->mod = new UserMod();

	}

	public function index() {
		$this->display();
	}

	/**
	 * 控制面板
	 */
	public function main() {

		$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		$endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

		//总注册
		$cond = ' mark = 1 ';
		$info['user'] = $this->mod->getCount($cond);

		//总分享
		$newsMod = m('news');
		$info['news_total'] = $newsMod->getCount($cond);

		//新增分享
		$condEx = "add_time >= {$beginToday} AND  add_time <= {$endToday} AND mark = 1 ";
		$info['news_today'] = $newsMod->getCount($condEx);

		//总校园圈
		$dynamicMod = m('dynamic');
		$info['dynamic_total'] =$dynamicMod->getCount($cond);

		//新增校园圈
		$info['dynamic_today'] = $dynamicMod->getCount($condEx);

		//总职位
		$jobMod = m('job');
		$info['job_total'] = $jobMod->getCount($cond);

		//新增职位
		$info['job_today'] = $jobMod->getCount($condEx);

		$this->assign('info',$info);


		$smod = m('statistics');

		//90天内新增人数统计
		//本日时间戳
		$t = time();

		$begin = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
		$endTime = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));

		//获取90天前的时间戳
		$startTime = strtotime('- 90day',$begin);

		//取出所有数据
		$cond = "type = 1 AND date_time >= {$startTime} AND date_time <={$endTime} AND mark = 1 ";

		$query = array(
			'fields' => 'id',
			'cond' => $cond,
			'order_by' => 'date_time ASC',
		);
		$data = $smod->getData($query);


		//数据处理
		$countList = array();
		foreach  ($data as $row) {
			$info = $smod->getInfo($row['id']);

			$day = date("Ymd", $info['date_time']);
			$countList[$day]+=$info['num'];
		}
		//printd($countList);
		$timeIndex = $startTime;
		$list = array();
		do {
			$day = date("Ymd", $timeIndex);
			$list[] = array(
				'date'=>date("Y-m-d", $timeIndex),
				'count'=>(int) $countList[$day]
			);
			$timeIndex+=(24*3600);
		}while ($timeIndex<$endTime);

		//组成字符串
		$date = [];
		$count = [];
		if ( is_array($list) ) {
			foreach ( $list as &$val ) {
				//$date[] = $val['date'];
				$date[] = '["'.$val['date'].'"]';

				$count[] = $val['count'];
			}
			unset($val);
		}

		$list['date'] = implode(',',$date);
		$list['count'] = implode(',',$count);

		$this->assign('list',$list);


		parent::index();
	}





	/**
	 * 获取菜单unick
	 */
	public function ajax_menu() {
		// 我的menu
		$my_menu = [];
		$admin_role_menu_mod = m('adminRoleMenu');
		$query = [
			'fields' => 'menu_id' ,
			'cond'   => 'mark=1 AND role_id=' . $this->userinfo['role_id'] ,
		];
		$my_menu_data = $admin_role_menu_mod->getData($query);
		foreach ($my_menu_data as $key => $value) {
			$my_menu[] = $value['menu_id'];
		}
		// 所有menu
		$admin_menu_mod = m('adminMenu');
		$query = [
			'cond'     => 'mark=1' ,
			'order_by' => 'sort ASC' ,
		];
		$menu = $admin_menu_mod->getData($query);
		$menu_tree = [];
		foreach ($menu as $key => $value) {
			if (0 == $value['pid']) {
				$menu_tree[$value['id']] = $value;
			} else {
				$menu_tree[$value['pid']]['child'][] = $value;
			}
		}
		// 数据加工
		$data = [];
		$child_menu_data = [];
		foreach ($menu_tree as $top_memu) {
			if (1 != $this->uid && !in_array($top_memu['id'] , $my_menu)) {
				continue;
			}
			$child_menu_data = [];
			if (is_array($top_memu['child'])) {
				foreach ($top_memu['child'] as $child_menu) {
					if (1 != $this->uid && !in_array($child_menu['id'] , $my_menu)) {
						continue;
					}
					$child_menu_data[] = [
						'title' => $child_menu['name'] ,
						'icon'  => 'fa fa-fw ' . $child_menu['class'] ,
						'href'  => $child_menu['controller'] ? '?app=' . $child_menu['controller'] . '&act=' . ($child_menu['action'] ? $child_menu['action'] : 'index') : '' ,
					];
				}
			}

			$data[] = [
				'title'    => $top_memu['name'] ,
				'icon'     => 'fa fa-fw ' . $top_memu['class'] ,
				'href'     => $top_memu['controller'] ? '?app=' . $top_memu['controller'] . '&act=' . ($top_memu['action'] ? $top_memu['action'] : 'index') : '' ,
				'spread'   => false ,
				'children' => $child_menu_data ,
			];
		}
		$this->ajaxReturn($data);
	}

	/**
	 * 上传logo
	 */
	public function upload_logo() {
		import('alioss.class');
		$res = Oss::upload('logo');
		$this->ajaxReturn($res);
	}

	/**
	 * @todo    上传附件
	 * @author  Malcolm  (2018年02月23日)
	 */
	public function upload() {
		import('alioss.class');

		$dateDir = date("/Y/m/d/H");
		$tmp = "attachment{$dateDir}";

		$res = Oss::upload($tmp);
		$this->ajaxReturn($res);
	}

	/**
	 * layedit上传图片
	 */
	public function upload_layedit() {
		import('alioss.class');
		$dateDir = date("/Y/m/d/H");
		$tmp = "attachment{$dateDir}";
		$res = Oss::upload($tmp);

		$data = [
			'code' => $res['status'] ? 0 : $res['code'] ,
			'msg'  => $res['msg'] ,
			'data' => [
				'src' => $res['data'][0] ? $res['data'][0] : '' ,
			] ,
		];
		$this->ajaxReturn($data);
	}

	/**
	 * 获取controller下面action
	 */
	public function ajax_action() {
		$data = $this->get_action(I('controller'));
		$this->ajaxReturn(R('9900' , true , $data));
	}

	/**
	 * 修改密码
	 */
	public function changepwd() {
		$oldPwd = md5(I('oldPwd'));
		$newPwd = md5(I('newPwd'));

		if (!I('newPwd')) {
			$this->ajaxReturn(R('9132'));
		}
		$admin_mod = &m('admin');
		$query = [
			'cond' => 'mark=1 AND id=' . $this->uid . ' AND password=\'' . $oldPwd . '\'' ,
		];
		$userinfo = $admin_mod->getOne($query);
		if (!$userinfo) {
			$this->ajaxReturn(R('9133'));
		}
		$data = [
			'id'       => $this->uid ,
			'password' => $newPwd ,
		];
		$res = $admin_mod->edit($data , $data['id']);
		if (!$res) {
			$this->ajaxReturn(R('9993'));
		}
		$this->ajaxReturn(R('9900' , true));
	}
}
