<?php

/**
 *  实时大屏控制器
 * Created by Malcolm.
 * Date: 2018/7/19  下午7:45
 */
class ScreenApp extends FrontendApp{
	public $mod,$relMod;

	public function __construct() {
		parent::__construct();

		$this->mod = m('opus');
		false&&$this->mod = new OpusMod();

		$this->relMod = m('opusVoteRel');
		false&&$this->relMod = new OpusVoteRelMod();

	}


	/**
	 * @todo    获取统计数据
	 * @author Malcolm  (2018年07月19日)
	 */
	public function getData() {
		$query = [
			'fields'   => 'id' ,
			'order_by' => 'vote_num DESC , id ASC' ,
			'cond'     => 'mark = 1',
			'limit'    => 10
		];

		$rs = $this->mod->getData($query);

		$data = [];
		if (is_array($rs)) {
			foreach ($rs as $key => $val) {
				$data[] = $this->mod->getListInfo($val['id']);
		    }
		}

		//echo  json_encode($data);
		$this->ajaxReturn($data);
	}



	public function index() {
		$this->display();
	}


	/**
	 * @todo    测试
	 * @author Malcolm  (2018年07月19日)
	 */
	public function test(){
		$this->display();
	}


}