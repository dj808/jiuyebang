<?php
class SignRecordMod extends CBaseMod {
    
    public function __construct() {
        parent::__construct("sign_record");
    }
	
	/**
	 * @todo    获取当前签到信息
	 * @author Malcolm  (2018年04月11日)
	 */
    public function getLastSignInfo($userId) {
        $query['cond'] = "user_id={$userId} AND type = 1 AND mark=1";
        $query['order_by'] = "id DESC";
        $lastInfo =  $this->getOne($query);
        //判断连续签到
        $yestertime = strtotime(date("Y-m-d", time()-(24*3600)));
        if ($lastInfo['add_time']<$yestertime) {
            $lastInfo['continuity_num'] = 0;
        }
        return $lastInfo;
    }
    
    
	/**
	 * @todo    获取当前月份的签到列表
	 * @author Malcolm  (2018年04月11日)
	 */
    public function getDayList($month='',$userId) {
        if (!$month) {
            $month = date("Y-m");
        }
        $startTime = strtotime($month);
        $endTime = strtotime(date("Y-m", $startTime+(31*24*3600)));
        $query['cond'] =  " user_id = {$userId} AND  add_time>{$startTime} AND add_time<{$endTime} AND mark=1";
        $query['fields'] = "sign_date";
        $query['order_by'] = "id ASC";
        $query['pri'] = "sign_date";
        $data = (array) $this->getData($query);
        
        $data = array_keys($data);
        if(!count($data))
	        $data = new StdClass();
        
        return $data;
    }
    
    
    
  
    
    
    
}