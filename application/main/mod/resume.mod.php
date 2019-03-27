<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/3/19
 * Time: 14:52
 */
class ResumeMod extends CBaseMod
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct('resume');
    }
    /**
     * @todo    获取简历信息
     * @author dingj  (2018年05月15日)
     */
    public function getResumeInfo($id)
    {
        $info=parent::getInfo($id);
        $userInfo =$this->userMod->getInfo($info['user_id']);
        $info['user_id']=$userInfo['nickname'];
        $info['add_time'] = $info['add_time'] ? date('Y-m-d H:i:s', $info['add_time']) : '';//添加时间
        $info['upd_time'] = $info['upd_time'] ? date('Y-m-d H:i:s', $info['upd_time']) : '';//更新时间
        $info['gender'] = $info['gender'] == 3 ? '保密' : ($info['gender'] == 1 ? '男' : '女');//性别的判断
        $info['graduated'] = $info['graduated'] == 1 ? '是' : '否';
        $info['city_name']= $this->cityMod->getCityName($info['area_id']);

        return $info;
     }


}