<?php
/**
 * Created by PhpStorm.
 * User: dj
 * Date: 2018/3/29
 * Time: 14:52
 */
class JobApplyMod extends CBaseMod
{

    /**
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct('job_apply');
        $this->typeList = [
            [
                'id'   => 1 ,
                'name' => '月薪'
            ] ,
            [
                'id'   => 2 ,
                'name' => '日薪'
            ] ,
            [
                'id'   => 3 ,
                'name' => '年薪'
            ],
            [
                'id'   => 4 ,
                'name' => '周薪'
            ],
            [
                'id'   => 5 ,
                'name' => '次日结'
            ]
        ];
        $this->statusList = [
            [
                'id'   => 1 ,
                'name' => '投递成功'
            ] ,
            [
                'id'   => 2 ,
                'name' => '已查看'
            ] ,
            [
                'id'   => 3 ,
                'name' => '待审核'
            ],
            [
                'id'   => 4 ,
                'name' => '审核通过'
            ],[
                 'id'   => 5 ,
                'name' => '审核未通过'
            ]
        ];
    }
    /**
     * @todo    获取职位申请信息
     * @author dingj  (2018年05月15日)
     */
    public function getJobInfo($id)
    {
        $info=parent::getInfo($id);

        //查询关联的用户
        $userInfo = $this->userMod->getInfo($info['user_id']);
        $info['user_id']=$userInfo['nickname'];
        //查询关联的职业
        $jobInfo =$this->jobMod->getInfo($info['job_id']);
        $info['job_id']=$jobInfo['title'];
        //查询关联的企业
        $companyInfo = $this->companyMod->getInfo($info['company_id']);
        $info['company_id']=$companyInfo['name'];
        $info['type']=$info['type']==1 ? '全职' : '兼职';
        $info['is_task']=$info['is_task']==1 ? '是' : '否';
        $info['add_time'] = $info['add_time'] ? date('Y-m-d H:i:s', $info['add_time']) : '';//添加时间
        $info['check_time'] = $info['check_time'] ? date('Y-m-d H:i:s', $info['check_time']) : '';
        switch ($info['status']){
            case 1:
                $info['status'] = '投递成功';
                break;

            case 2:
                $info['status'] = '已查阅';
                break;

            case 3:
                $info['status'] = '待审核';
                break;

            case 4:
                $info['status'] = '审核通过';
                break;

            case 5:
                $info['status'] = '审核未通过';
                break;

            default:
                $info['status'] = '投递成功';
        }
            return $info;
     }
    /**
     * @todo    获取职位列表
     * @author Malcolm  (2018年05月15日)
     */
    public function getJobList(){
        $cond[] = " mark = 1";

        $jobMod= $this->jobMod;
        $ids = $jobMod->getIds($cond);
        $data = [];
        if ( is_array($ids) ) {
            foreach ( $ids as $key => $val ) {
                $info = $jobMod->getInfo($val);
                $data[] = [
                    'id' => $info['id'],
                    'name' => $info['title'],
                ];
            }
        }

        return $data;
    }

}