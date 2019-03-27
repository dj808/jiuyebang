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
   

}