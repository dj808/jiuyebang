<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/3/19
 * Time: 14:52
 */
class CateMod extends CBaseMod
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct('cate');
    }
    /**
     * @todo    获取分类信息
     * @author dingj  (2018年05月15日)
     */
    public function getCateInfo($id)
    {
        $info=parent::getInfo($id);
        $info['add_time'] = $info['add_time'] ? date('Y-m-d H:i:s', $info['add_time']) : '';//添加时间
        $info['upd_time'] = $info['upd_time'] ? date('Y-m-d H:i:s', $info['upd_time']) : '';//添加时间
         //登录状态
        switch ($info['type']) {
            case '1':
                $info['type'] = "秘籍";
                break;
            case '2':
                $info['type'] = "试卷";
                break;
            case '3':
                $info['type'] = "攻略";
                break;
            case '4':
                $info['type'] = "每日分享";
                break;
            default:
                $info['type'] = "秘籍";
        }
        return $info;
     }


}