<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/3/19
 * Time: 14:52
 */
class MajorMod extends CBaseMod
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct('major');
    }
    /**
     * @todo    获取专业信息
     * @author dingj  (2018年05月15日)
     */
    public function getMajorInfo($id)
    {
         $info=parent::getInfo($id);
         $info['add_time'] = $info['add_time'] ? date('Y-m-d H:i:s', $info['add_time']) : '';//添加时间
         $info=$this->getInfo($id);
         $data = $this->getInfo($info['pid']);
         $info['pid_name']=$data['name'];

         return $info;
     }


}