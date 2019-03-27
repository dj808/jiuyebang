<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/3/19
 * Time: 14:52
 */
class CommentMod extends CBaseMod
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct('comment');
        $this->typeList=[
            [
                'id'   => 1 ,
                'name' => '早读趣事'
            ] ,
            [
                'id'   => 2 ,
                'name' => '秘籍'
            ] ,
            [
                'id'   => 3 ,
                'name' => '攻略'
            ] ,
            [
                'id'   => 4 ,
                'name' => '试卷'
            ] ,
            [
                'id'   => 5 ,
                'name' => '互助'
            ] ,
            [
                'id'   => 6 ,
                'name' => '校园趣事'
            ] ,

        ];
    }
    /**
     * @todo    获取评论信息
     * @author dingj  (2018年05月15日)
     */
    public function getCommentInfo($id)
    {
        $info = parent::getInfo($id);
         $info['add_time'] = $info['add_time'] ? date('Y-m-d H:i:s', $info['add_time']) : '';//添加时间
         $info['state'] = $info['state'] == 1 ? '已通过' : ($info['state'] == 2 ? '未通过' : '待审核');//实名认证的判断
         //获取用户昵称
         $userInfo = $this->userMod->getInfo($info['user_id']);
         $info['username']=$userInfo['nickname'];
         //获取状态
         $info['state_name']=[
                             1=>'已通过',
                             2=>'未通过',
                             3=>'待审核'
         ];
         $info['reply_level']=$info['reply_level']==1 ? '一级' : '二级';
        //登录状态
        switch ($info['type']) {
            case '1':
                $info['type'] = "早读趣事";
                $data=$this->newsMod->getInfo($info['type_id']);
                $info['type_name']=$data['title'];
                break;
            case '2':
                $info['type'] = "秘籍";
                $data=$this->raidersMod->getInfo($info['type_id']);
                $info['type_name']=$data['title'];
                break;
            case '3':
                $info['type'] = "攻略";
                $data=$this->raidersMod->getInfo($info['type_id']);
                $info['type_name']=$data['title'];
                break;
            case '4':
                $info['type'] = "试卷";
                $data=$this->raidersMod->getInfo($info['type_id']);
                $info['type_name']=$data['title'];
                break;
            case '5':
                $info['type'] = "互助";
                $data=$this->cooperationMod->getInfo($info['type_id']);
                $info['type_name']=$data['title'];
                break;
            case '6':
                $info['type'] = "校园趣事";
                $data=$this->dynamicMod->getInfo($info['type_id']);
                $info['type_name']=$data['content'];
                break;
            default:
                $info['type'] = "早读趣事";
                $data=$this->newsMod->getInfo($info['type_id']);
                $info['type_name']=$data['title'];
        }
        return $info;
    }


}