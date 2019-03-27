<?php 
 
class ConfigMod extends CBaseMod {
    
    public $type = [
        1=>"文本",
        2=>"富文本",
        3=>"图片",
        4=>"自定义"
    ];
    public function __construct() {
        parent::__construct('config');
    }
     
    public function getInfo($id) {
        $info = parent::getInfo($id); 
        $info['content'] = unserialize($info['content']);   
        return $info;
    }
    
    public function getInfoByTag($tag) {
        $info = $this->getRowByField("tag", $tag);
        return unserialize($info['content']);
    }
	
	
	/**
	 * @todo    获取列表数据信息
	 * @author Malcolm  (2018年02月19日)
	 */
    public function getListInfo($id){
        $info = $this->getInfo($id);
	
	    $info['type_name'] = $this->type[$info['type']];
	    
     
	    return $info;
    }

}