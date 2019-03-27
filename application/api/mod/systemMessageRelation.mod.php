<?php

class SystemMessageRelationMod extends CBaseMod {
    
    public function __construct() {
        parent::__construct("system_message_relation");
    }
    
    public function getInfo($id) {
        $info = parent::getInfo($id);
        $mainMod = m("systemMessage");
        $mainInfo = $mainMod->getInfo($info['message_id']);
        return [
            'system_message_id'=>$id,
            'title'=>$mainInfo['title'],
            'content'=>$mainInfo['content'],
            
            'date'=>date("Y-m-d H:i:s",$info['add_time']),
            'is_read'=>$info['is_read']
        ];
    }
}