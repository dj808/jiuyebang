<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/3/30
 * Time: 14:52
 */

/*
* @todo 互助管理模型
* @author dingj (2018年4月23日)
*/
class CooperationMod extends CBaseMod
{

    /**
     * 构造函数
     */
    public function __construct()
    {

        parent::__construct('cooperation');

    }
	
	
	/**
	 * @todo    获取互助列表数据信息
	 * @author dingj  (2018年04月23日)
	 */
    public function getListInfo($id){

        $info = parent::getInfo($id);
        $info['add_time'] = $info['add_time'] ? date('Y-m-d H:i:s', $info['add_time']) : '';//添加时间

	    $info['type'] = $info['type'] == 1 ? '转让' : '互助';
	    $info['status'] = $info['status'] == 1 ? '未完成' : '已完成';
	    $userInfo = $this->userMod->getInfo($info['user_id']);
	    $info['username']=$userInfo['nickname'];
	    $info['city_name']= $this->cityMod->getCityName($info['area_id']);

	    return $info;
    }
}