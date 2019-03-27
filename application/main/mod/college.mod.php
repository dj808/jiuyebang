<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/3/19
 * Time: 14:52
 */
class CollegeMod extends CBaseMod
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct('college');
    }
    /**
     * @todo    获取城市信息
     * @author dingj  (2018年05月15日)
     */
    public function getCollegeInfo($id)
    {
        $info=parent::getInfo($id);
        $info['grade'] = $info['grade'] == 1 ? '本科' :'专科';//性别的判断
        $info['is_private'] = $info['is_private'] == 1 ? '是' : '否';
        $info['is_211'] = $info['is_211'] == 1 ? '是' : '否';
        $info['is_985'] = $info['is_985'] == 1 ? '是' : '否';
        switch ($info['batch']) {
            case '0':
                $info['batch'] = "";
                break;
            case '1':
                $info['batch'] = "一本";
                break;
            case '2':
                $info['batch'] = "二本";
                break;
            case '3':
                $info['batch'] = "三本";
                break;
            default:
                $info['batch'] = "";
        }
        return $info;
     }


}