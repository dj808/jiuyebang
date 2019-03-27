<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/3/19
 * Time: 14:52
 */
class JobIndustryMod extends CBaseMod
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct('job_industry');
    }
    /**
     * @todo    获取专业信息
     * @author dingj  (2018年05月15日)
     */
    public function getJobIndustryInfo($id)
    {
         $info=parent::getInfo($id);
         $info['add_time'] = $info['add_time'] ? date('Y-m-d H:i:s', $info['add_time']) : '';//添加时间
         $info['logo']=$info['icon'];
         return $info;
     }


}