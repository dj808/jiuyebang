<?php
class AdMod extends CBaseMod {
    
    public function __construct() {
        parent::__construct("ad");
    }
	
	
	/**
	 * @todo    根据广告位获取广告列表
	 * @author Malcolm  (2018年04月12日)
	 */
    public function getListByPosition($position,$android=false) {
        $cond = " ad_position='{$position}' AND mark=1 ";
        $ids = $this->getIds($cond);
	
        $list = [];
	    if ( is_array($ids) ) {
		    foreach ( $ids as $key => $val ) {
			    $info = $this->getInfo($val);


			    //安卓直播特别处理
			    if($info['id'] == 2 && $android)
			        $info['content'] = 'https://mp.weixin.qq.com/s/EciYCZM8Cy9ZJ9scau9Olw';


			    $list[] = [
				    'type'=>$info['type'],
				    'cover_url'=>$info['cover'],
				    'plate'=>$info['plate'],
				    'title'=>$info['title'],
				    'type_id'=>$info['type_id'],
				    'is_need_login'=>$info['is_need_login'],
				    'url'=>$info['content'],
				    'share_url'=>$info['share_url'],
			    ];
            }
	    }
	    
        return $list;
    }
}