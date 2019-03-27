<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/3/29
 * Time: 14:52
 */
class CollectMod extends CBaseMod
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct('collect');
        $this->typeList = [
            [
                'id'   => 1 ,
                'name' => '全职'
            ] ,
            [
                'id'   => 2 ,
                'name' => '兼职'
            ],
            [
                'id'   => 3,
                'name' => '趣事'
            ],
            [
                'id'   => 4 ,
                'name' => '攻略'
            ],
            [
                'id'   => 5 ,
                'name' => '试卷'
            ],
            [
                'id'   => 6 ,
                'name' => '秘籍'
            ],
            [
                'id'   => 7 ,
                'name' => '资讯'
            ],
            [
                'id'   => 8 ,
                'name' => '培训'
            ]
        ];
    }

    /**
     * @todo    获取互助申请信息
     * @author dingj  (2018年05月15日)
     */
    public function getCollectInfo($id)
    {
        $info=parent::getInfo($id);

        //查询关联的用户
        $userInfo = $this->userMod->getInfo($info['user_id']);
        $info['username']=$userInfo['nickname'];
        $info['add_time'] = $info['add_time'] ? date('Y-m-d H:i:s', $info['add_time']) : '';//添加时间

        //收藏类型
        switch ($info['type']) {
            case '1':
                $info['type'] = "全职";
                $data=$this->jobMod->getInfo($info['type_id']);
                $info['type_id']=$data['title'];
                break;
            case '2':
                $info['type'] = "兼职";
                $data=$this->jobMod->getInfo($info['type_id']);
                $info['type_id']=$data['title'];
                break;
            case '3':
                $info['type'] = "趣事";
                $data=$this->funMod->getInfo($info['type_id']);
                $info['type_id']=$data['title'];
                break;
            case '4':
                $info['type'] = "攻略";
                $data=$this->raidersMod->getInfo($info['type_id']);
                $info['type_id']=$data['title'];
                break;
            case '5':
                $info['type'] = "试卷";
                $data=$this->raidersMod->getInfo($info['type_id']);
                $info['type_id']=$data['title'];
                break;
            case '6':
                $info['type'] = "秘籍";
                $data=$this->raidersMod->getInfo($info['type_id']);
                $info['type_id']=$data['title'];
                break;
            case '7':
                $info['type'] = "资讯";
                $data=$this->newsMod->getInfo($info['type_id']);
                $info['type_id']=$data['title'];
                break;
            case '8':
                $info['type'] = "培训";
                $data=$this->trainingMod->getInfo($info['type_id']);
                $info['type_id']=$data['title'];
                break;
            default:
                $info['type'] = "全职";
        }
       return $info;
     }


}