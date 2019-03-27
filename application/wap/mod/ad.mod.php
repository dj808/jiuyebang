<?php
class AdMod extends CBaseMod {
    
    public function __construct() {
        parent::__construct("ad");
    }
	
	
	/**
	 * @todo    根据广告位获取广告列表
	 * @author Malcolm  (2018年04月12日)
	 */
    public function getListByPosition($position) {
        $cond = " ad_position='{$position}' AND mark=1 ";
        $ids = $this->getIds($cond);
	
        $list = [];
	    if ( is_array($ids) ) {
		    foreach ( $ids as $key => $val ) {
			    $info = $this->getInfo($val);
			
			    $list[] = [
				    'type'=>$info['type'],
				    'cover_url'=>$info['cover'],
				    'plate'=>$info['plate'],
				    'title'=>$info['title'],
				    'type_id'=>$info['type_id'],
				    'is_need_login'=>$info['is_need_login'],
				    'url'=>$info['content']
			    ];
            }
	    }
	    
        return $list;
    }
}