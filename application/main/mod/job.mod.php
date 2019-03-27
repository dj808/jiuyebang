<?php
/**
 * 职位模型
 * User: dj
 * Date: 2018/3/19
 * Time: 14:52
 */

class JobMod extends CBaseMod
{
    public $typeList;
    /**
     * 构造函数
     */
    public function __construct()
    {

        parent::__construct('job');
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
            ],
            [
                'id'   =>6,
                'name' => '时薪'
            ]
        ];
        $this->statusList = [
            [
                'id'   => 1 ,
                'name' => '已通过'
            ] ,
            [
                'id'   => 2 ,
                'name' => '待审核'
            ] ,
            [
                'id'   => 3 ,
                'name' => '未通过'
            ]
        ];
    }

	/**
	 * @todo    获取职位信息
	 * @author   dingj  (2018年04月13日)
	 */
    public function getListInfo($id){
        $info = parent::getInfo($id);

        $info['add_time'] = $info['add_time'] ? date('Y-m-d H:i:s', $info['add_time']) : '';//开始时间
	    $info['upd_time'] = $info['upd_time'] ? date('Y-m-d H:i:s', $info['upd_time']) : '';//结束时间
	    $info['type'] = $info['type'] == 1 ? '全职' : '兼职';
	    $info['status'] = $info['status'] == 1 ? '开' : '关';
	    $info['auth_status']=$info['auth_status']==1 ? '已通过':($info['auth_status']==2 ? '待审核':'未通过');
	    $companyInfo = $this->companyMod->getInfo($info['company_id']);
	    $info['company_id']=$companyInfo['name'];

	    if($info['dist_id'])
	       $info['city_name']= $this->cityMod->getCityName($info['dist_id']);
	    else
           $info['city_name']= $this->cityMod->getCityName($info['city_id']);
	    //获取报名人数
        $cond=[
            'job_id'=>$id,
            'mark'=>1
        ];
        $info['apply_num']= $this->jobApplyMod->getCount($cond);
	    //薪水类型
	    switch ($info['money_type']) {
		    case '1':
			    $info['money_type'] = "月薪";
			    break;
		    case '2':
			    $info['money_type'] = "日薪";
			    break;
		    case '3':
			    $info['money_type'] = "年薪";
			    break;
		    case '4':
			    $info['money_type'] = "周薪";
			    break;
            case '5':
                $info['money_type'] = "次日结";
                break;
            case '6':
                $info['money_type'] = "时薪";
                break;
		    default:
			    $info['money_type'] = "月薪";
	    }
	    return $info;
	    
    }

    /**
     * @todo    获取公司列表
     * @author Malcolm  (2018年05月15日)
     */
    public function getCompanyList(){
        $cond[] = "status=1 AND mark = 1";

        $companyMod= $this->companyMod;
        $ids = $companyMod->getIds($cond);
        $data = [];
        if ( is_array($ids) ) {
            foreach ( $ids as $key => $val ) {
                $info = $companyMod->getInfo($val);
                $data[] = [
                    'id' => $info['id'],
                    'name' => $info['name'],
                ];
            }
        }

        return $data;
    }

}