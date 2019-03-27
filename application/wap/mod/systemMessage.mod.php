<?php

class SystemMessageMod extends CBaseMod {
    
    public function __construct() {
        parent::__construct("system_message");
    }
    
    public function getInfo($id) {
        $info = parent::getInfo($id);
        return [
            'system_message_id'=>$id,
            'title'=>$info['title'],
            'content'=>$info['content'],
            'date'=>date("Y-m-d H:i:s",$info['add_time']),
            'is_read'=>$info['is_read']
        ];
        
    }
	
	
	/**
	 * @todo    获取未读消息数量
	 * @author Malcolm  (2017年12月06日)
	 */
    public function getNoReadNum($userId){
        $cond = " user_id = {$userId} AND  is_read = 2 AND mark = 1 ";
        $count = $this->systemMessageRelationMod->getCount($cond);
        
        return $count;
    }
    
    
}