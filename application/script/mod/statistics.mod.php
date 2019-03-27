<?php

/**
 * 统计模型
 * Created by Malcolm.
 * Date: 2018/3/6  15:24
 */
class StatisticsMod extends CBaseMod {
	public function __construct() {
		parent::__construct('statistics');
	}


	/**
	 * @todo    维护
	 * @author  Malcolm  (2018年07月14日)
	 */
	public function manage($type , $num , $date = '') {
		if (!$date)
			$date = date('Y-m-d' , time());

		//先查有没有该日期的数据
		$cond = " type = {$type} AND `date` = '{$date}' AND mark = 1  ";

		$ids = $this->getIds($cond);

		if (empty($ids))
			$id = ''; else
			$id = $ids[0];

		$rs = $this->edit([
			'type'      => $type ,
			'num'       => $num ,
			'date'      => $date ,
			'date_time' => strtotime($date)+360 ,
		] , $id);


		if (!$rs)
			return false;

		return true;
	}

}