 <?php
/**
 * 优惠券信息管理
 * @author luw
 * @data 2017-04-10
 */
class CouponMod extends CBaseMod
{

    public function __construct() {
        parent::__construct('coupon');

    }

	/*
	 *获取基本信息
	 * @author luw
 	 * @data 2017-04-10
    */
	public function getInfo($id){
		$info = parent::getInfo($id);

		return $info;
	}

	

	public function statusType($status = 0) {
		$typeArr = array(
            1   => '启用',
            2   => '停用',
        );
        return $status ? $typeArr[$status] : $typeArr;
	}
	
	
	/**
	 * @todo    获取优惠券列表
	 * @author wangqs  (2017年04月13日)
	 */
	public function getCouponList($type=1){
		$cond = "type= {$type} AND status = 1 AND  mark = 1 ";
		$ids = $this->getIds($cond);
		
		$list = [];
		if ( is_array($ids) ) {
			foreach ( $ids as $key => $val ) {
				$list[] = $this->getInfo($val);
		    }
		}
		
		return $list;
	}
	
	
	/**
	 * @todo    获取优惠券有效期
	 * @author wangqs  (2017年04月14日)
	 * @param   int  $couponId 优惠券ID
	 * @param int $model  返回模式：1时间戳  2格式化的Y-m-d
	 * @return  array
	 */
	public function getCouponEffectiveDate($couponId, $model = 1) {
		$couponInfo = $this->getInfo($couponId);
		$duration = intval($couponInfo['duration']);
		$now = time();
		if ($model == 1) {
			$dateArr = array(
				'start_time' => $now,
				'end_time' => strtotime(date('Y-m-d').' 23:59:59') + $duration * 86400
			);
		} else {
			$dateArr = array(
				'start_time' => date('Y-m-d', $now),
				'end_time' => date('Y-m-d', strtotime(date('Y-m-d').' 23:59:59') + $duration * 86400)
			);
		}
		return $dateArr;
	}
	
	
	
}