<?php

/**
 *  发布拓展控制器
 * Created by Malcolm.
 * Date: 2018/6/12  10:04
 */
class Publish extends Zeus {
    public $jobMod , $userMod , $companyMod;

    public function __construct() {
        parent::__construct("job");

        $this->jobMod = m('job');
        false && $this->jobMod = new JobMod();

        $this->userMod = m('user');
        false && $this->userMod = new UserMod();

        $this->companyMod = m('company');
        false && $this->companyMod = new CompanyMod();


    }


    /**
     * @todo    获取职位选项列表
     * @author  Malcolm  (2018年06月12日)
     */
    public function getJobOptionList() {
        //职位类型
        $jobTypeListTmp = Zeus::config('job_type_list');

        $jobTypeList = [];
        if (is_array($jobTypeListTmp)) {
            foreach ($jobTypeListTmp as $key => $val) {
                $jobTypeList[] = [
                    'type_id'   => $key ,
                    'type_name' => $val ,
                ];
            }
        }

        //职位标签
        $jobTagListTmp = Zeus::config('job_tag');

        $jobTagList = [];
        if (is_array($jobTagListTmp)) {
            foreach ($jobTagListTmp as $key => $val) {
                $jobTagList[] = [
                    'tag_id'   => $key ,
                    'tag_name' => $val ,
                ];
            }
        }

        //行业
        $jobIndustryListTmp = m('job_industry')->getData(['cond' => 'mark = 1','order_by' => 'sort DESC']);

        $jobIndustryList = [];

        if (is_array($jobIndustryListTmp)) {
            foreach ($jobIndustryListTmp as $key => $val) {
                $jobIndustryList[] = [
                    'industry_id'   => $val['id'] ,
                    'industry_name' => $val['name'] ,
                ];
            }
        }
        
        //薪水类型
        $partTypeListTmp = [
            [
                'id'   => 1 ,
                'name' => '月结',
            ] ,
            [
                'id'   => 2 ,
                'name' => '日结',
            ] ,
            [
                'id'   => 4 ,
                'name' => '周结',
            ],
            [
                'id'   => 5 ,
                'name' => '次日结',
            ]
        ];
        

        $partTypeList = [];
        if (is_array($partTypeListTmp)) {
            foreach ($partTypeListTmp as $key => $val) {
                $partTypeList[] = [
                    'money_type_id'   => $val['id'] ,
                    'money_type_name' => $val['name'] ,
                ];
            }
        }

        //薪水类型
        $moneyTypeListTmp = [
            [
                'id'   => 1 ,
                'name' => '月薪',
            ] ,
            [
                'id'   => 2 ,
                'name' => '日薪',
            ] ,
            [
                'id'   => 3 ,
                'name' => '年薪',
            ] ,
            [
                'id'   => 4 ,
                'name' => '周薪',
            ]
        ];
        

        $moneyTypeList = [];
        if (is_array($moneyTypeListTmp)) {
            foreach ($moneyTypeListTmp as $key => $val) {
                $moneyTypeList[] = [
                    'money_type_id'   => $val['id'] ,
                    'money_type_name' => $val['name'] ,
                ];
            }
        }
        //联系方式
        $linkTypeList = [
            [
            'link_type_id'   => 1,
            'link_type_name' => '手机',
            ],
            [
            'link_type_id'   => 2,
            'link_type_name' => '微信',
            ],
            [
            'link_type_id'   => 3,
            'link_type_name' => 'QQ',
            ]
        ];

        //性别要求
        $sexListListTmp = [
            0 => '不限' ,
            1 => '男' ,
            2 => '女' ,
        ];

        $sexListList = [];

        if (is_array($sexListListTmp)) {
            foreach ($sexListListTmp as $key => $val) {
                $sexListList[] = [
                    'sex_id'   => $key ,
                    'sex_name' => $val ,
                ];
            }
        }

        //学历列表
        $educationListTmp = Zeus::config('edu_list');

        $educationList = [];
        if (is_array($educationListTmp)) {
            foreach ($educationListTmp as $key => $val) {
                $educationList[] = [
                    'education_id'   => $key ,
                    'education_name' => $val ,
                ];
            }
        }

        //经验列表
        $experienceListTmp = Zeus::config('job_time_list');

        $experienceList = [];

        if (is_array($experienceListTmp)) {
            foreach ($experienceListTmp as $key => $val) {
                $experienceList[] = [
                    'experience_id'   => $key ,
                    'experience_name' => $val ,
                ];
            }
        }

        $data = [
            'job_type_list'       => $jobTypeList ,
            'job_tag_list'        => $jobTagList ,
            'job_industry_list'   => $jobIndustryList ,
            'job_money_type_list' => $moneyTypeList ,
            'job_money_part_list' => $partTypeList,
            'job_sex_list'        => $sexListList ,
            'job_education_list'  => $educationList ,
            'job_experience_list' => $experienceList ,
            'job_link_type_List'  => $linkTypeList
        ];

        return message('操作成功' , true , $data);
    }


    /**
     * @todo    发布职位
     * @author  Malcolm  (2018年06月12日)
     */
    public function addJob($param , $userId) {
        //查看用户类型
        $userInfo = $this->userMod->getInfo($userId);
        if ($userInfo['type'] == 1)
            return message('非企业用户无法发布' , false);


        //获取公司ID
        $companyId = $this->companyMod->getCompanyIdByUserId($userId);
        if (!$companyId)
            return message('非企业用户无法发布' , false);


        $type = $param['type'];
        if (!$type)
            return message('请选择招聘类型');
        
        //是否为兼职
        switch ($type){
            //全职
            case 1:
                $jobTypeIds = $param['job_type_ids'];
                if (!$jobTypeIds)
                    return message('请选择所属行业');

                $tagIds = $param['tag_ids'];
                if (!$tagIds)
                    return message('请选择职位标签');

                $industryId = $param['industry_id'];
                if (!$industryId)
                    return message('请选择岗位类型');

                $title = $param['title'];
                if (!$title)
                    return message('请填写职位名称');

                $content = $param['content'];
                if (!$content)
                    return message('请填写职位内容');

                $numLower = $param['num_lower'];
                if ($numLower == "")
                    return message('请填写最低招聘人数');

                $numUpper = $param['num_upper'];
                if ($numUpper == "")
                    return message('请填写最高招聘人数');

                $moneyType = $param['money_type'];
                if (!$moneyType)
                    return message('请选择薪资类型');

                $moneyLower = $param['money_lower'];

                $moneyUpper = $param['money_upper'];

                if (!$moneyLower)
                    return message('请填写最低工资标准');

                if (!$moneyUpper)
                    return message('请填写最高工资标准');

                $language = $param['language'];
                if (!$language)
                    return message('请填写语言要求');

                $education = $param['education'];
                if (!$education)
                    return message('请选择学历要求');

                $experience = $param['experience'];
                if ($experience == "")
                    return message('请选择工作经验要求');

                $timeStart = $param['time_start'];
                if (!$timeStart)
                    return message('请填写开始时间');

                $timeEnd = $param['time_end'];
                if (!$timeEnd)
                    return message('请填写结束时间');

                $provId = $param['prov_id'];
                if (!$provId)
                    return message('请选择省份');


                $cityId = $param['city_id'];
                if (!$cityId)
                    return message('请选择城市');


                $distId = $param['dist_id'];
                if (!$distId)
                    return message('请选择区县');


                $address = $param['address'];
                if (!$address)
                    return message('请填写详细地址');


                $linkPerson = $param['link_person'];
                if (!$linkPerson)
                    return message('请填写联系人');


                $linkPhone = $param['link_phone'];
                if (!$linkPhone)
                    return message('请填写联系电话');
                if(!preg_match('/^13\d{9}$|^14\d{9}$|^15\d{9}$|^17\d{9}$|^18\d{9}$/',$linkPhone))
                    return message('请填写正确的手机号码');

                $companyId = m('company')->getCompanyIdByUserId($userId);

                $data = [
                    'company_id'   => $companyId ,
                    'type'         => $type ,
                    'job_type_ids' => $jobTypeIds ,
                    'tag_ids'      => $tagIds ,
                    'industry_id'  => $industryId ,
                    'title'        => $title ,
                    'content'      => '<p>' . $content . '</p>' ,
                    'num_lower'    => $numLower ,
                    'num_upper'    => $numUpper ,
                    'money_type'   => $moneyType ,
                    'money_lower'  => $moneyLower ,
                    'money_upper'  => $moneyUpper ,
                    'sex'          => $param['sex'] ,
                    'language'     => $language ,
                    'education'    => $education ,
                    'experience'   => $experience ,
                    'date_start'   => $dateStart ,
                    'date_end'     => $dateEnd ,
                    'time_start'   => $timeStart ,
                    'time_end'     => $timeEnd ,
                    'prov_id'      => $provId ,
                    'city_id'      => $cityId ,
                    'dist_id'      => $distId ,
                    'address'      => $address ,
                    'link_person'  => $linkPerson ,
                    'link_phone'   => $linkPhone ,
                    'add_user'     => $userId ,
                    'auth_status'  => 2 ,
                ];
                break;
            //兼职
            case 2:
                $title = $param['title'];
                if (!$title)
                    return message('请填写职位名称');
                
                $provId = $param['prov_id'];
                if (!$provId)
                    return message('请选择省份');


                $cityId = $param['city_id'];
                if (!$cityId)
                    return message('请选择城市');


                $distId = $param['dist_id'];
                if (!$distId)
                    return message('请选择区县');


                $address = $param['address'];
                if (!$address)
                    return message('请填写详细地址');
                
                $numLower = $param['num_lower'];
                if (!$numLower)
                    return message('请填写招聘人数');

                $ageLower = $param['age_lower'];
                if ($ageLower == "")
                    return message('请填写最低年龄');

                $ageUpper = $param['age_upper'];
                if ($ageUpper  == "")
                    return message('请填写最高年龄');
                
                $sex = $param['sex'];
                if ($sex == "")
                    return message('请选择性别要求');
                
                $experience = $param['experience'];
                if ($experience == "")
                    return message('请选择工作经验要求');
                
                $moneyType = $param['money_type'];
                if (!$moneyType)
                    return message('请选择薪资类型');
                
                $moneyUpper = $param['money_upper'];
                if (!$moneyUpper)
                    return message('请填写薪资待遇');

                $industryId = $param['industry_id'];
                if (!$industryId)
                    return message('请选择岗位类型');
                
                $tagIds = $param['tag_ids'];
                if (!$tagIds)
                    return message('请选择职位标签');

                $timeGroup = $param['time_group'];
                
                //7.12
                $worktimeId = 3;
                if (!$timeGroup){
                    return message('请选择上班时间');
                }else{
                    //判断上班时间
                    $worktimes = explode(',',$timeGroup);
                    foreach($worktimes as $worktime){
                        $week = date('w',strtotime($worktime));
                        if(in_array($week,[6,0])){
                            $worktimeId = $worktimeId == 1||$worktimeId == 0?0:2;
                        }else{
                            $worktimeId = $worktimeId == 2||$worktimeId == 0?0:1;
                        }
                    }
                }
                $is_continue = $param['is_continue'];
                if (!$is_continue)
                    return message('请选择是否连做');
                
                $is_interview = $param['is_interview'];
                if (!$is_interview)
                    return message('请选择是否需要面试');
                
                $linkPerson = $param['link_person'];
                if (!$linkPerson)
                    return message('请填写联系人');
                
                $linkType = $param['link_type'];
                if (!$linkType)
                    return message('请选择联系方式');
                
                $linkPhone = $param['link_phone'];
                if (!$linkPhone)
                    return message('请填写该联系方式号码');
                if($param['link_type'] == 1&&!preg_match('/^13\d{9}$|^14\d{9}$|^15\d{9}$|^17\d{9}$|^18\d{9}$/',$linkPhone))
                    return message('请填写正确的手机号码');
                
                $content = $param['content'];
                if (!$content)
                    return message('请填写职位内容');
                
                $companyId = m('company')->getCompanyIdByUserId($userId);
                
                $data = [
                    'company_id'   => $companyId ,
                    'type'         => $type ,
                    'industry_id'  => $industryId ,
                    'tag_ids'      => $tagIds ,
                    'title'        => $title ,
                    'content'      => '<p>' . $content . '</p>' ,
                    'num_lower'    => $numLower ,
                    'num_upper'    => $numUpper ,
                    'money_type'   => $moneyType ,
                    'money_upper'  => $moneyUpper ,
                    'sex'          => $param['sex'] ,
                    'experience'   => $experience ,
                    'prov_id'      => $provId ,
                    'city_id'      => $cityId ,
                    'dist_id'      => $distId ,
                    'address'      => $address ,
                    'link_person'  => $linkPerson ,
                    'link_phone'   => $linkPhone ,
                    'add_user'     => $userId ,
                    'auth_status'  => 2 ,
                    'time_group'   => $timeGroup,
                    'age_lower'    => $age_lower,
                    'age_upper'    => $age_upper,
                    'is_continue'  => $is_continue,
                    'is_interview' => $is_interview,
                    'link_type'    => $linkType,
                    'worktime_id'  => $worktimeId
                ];
                break;
        }
        

        $id = $param['id'] ? intval($param['id']) : 0;

        $info = $this->jobMod->getInfo($id);
        if ($id && ($info['company_id'] != $companyId)) {
            return message('仅能修改自己发布的职位');
        }

        if ($id) {
            //审核未通过修改
            if ($info['auth_status'] == 3)
                $data['auth_status'] = 2;

            //如果已经通过了审核
            if ($info['auth_status'] == 1)
                $data['auth_status'] = 1;
        }


        $rs = $this->jobMod->edit($data , $id);

        if (!$rs)
            return message('系统繁忙，请稍候再试');

        //维护经纬度
        Zeus::push('gpsManage' , ['type' => 'job' , 'typeId' => $rs]);

        //发送推送
        if ($data['auth_status'] != 1) {
            if (!$id) {
                $title = '成功发布招聘信息';
                $content = "您已成功发布招聘信息【{$data['title']}】,我们会尽快为你审核处理";
            }
            else {
                $title = '成功修改招聘信息';
                $content = "您已成功修改招聘信息【{$data['title']}】,我们会尽快为你审核处理";
            }


            Zeus::sendMsg([
                'type'      => ['msg' , 'push'] ,
                'user_id'   => $userId ,
                'title'     => $title ,
                'content'   => $content ,
                'msg_type'  => 1 ,
                'user_type' => 1 ,
            ]);


        }


        return message('操作成功' , true);
    }


    /**
     * @todo    发布培训时，获取选项列表
     * @author  Malcolm  (2018年06月14日)
     */
    public function getTrainOptionList() {
        //行业
        $trainIndustryListTmp = Zeus::config('train_industry_list');

        $trainIndustryList = [];

        if (is_array($trainIndustryListTmp)) {
            foreach ($trainIndustryListTmp as $key => $val) {
                $trainIndustryList[] = [
                    'industry_id'   => $key ,
                    'industry_name' => $val ,
                ];
            }
        }


        //级别
        $trainLevelListTmp = Zeus::config('train_level_list');

        $trainLevelList = [];

        if (is_array($trainLevelListTmp)) {
            foreach ($trainLevelListTmp as $key => $val) {
                $trainLevelList[] = [
                    'level_id'   => $key ,
                    'level_name' => $val ,
                ];
            }
        }

        //周期
        $trainCycleListTmp = Zeus::config('train_cycle_list');

        $trainCycleList = [];

        if (is_array($trainCycleListTmp)) {
            foreach ($trainCycleListTmp as $key => $val) {
                $trainCycleList[] = [
                    'cycle_id'   => $key ,
                    'cycle_name' => $val ,
                ];
            }
        }


        $data = [
            'industry_list' => $trainIndustryList ,
            'level_list'    => $trainLevelList ,
            'cycle_list'    => $trainCycleList ,
        ];


        return message('操作成功' , true , $data);
    }


    /**
     * @todo    发布培训
     * @author  Malcolm  (2018年06月14日)
     */
    public function addTrain($param , $userId) {
        $industryId = trim($param['industry_id']);
        $levelId = trim($param['level_id']);
        $cycleId = trim($param['cycle_id']);
        $title = trim($param['title']);
        $subTitle = trim($param['sub_title']);
        $price = trim($param['price']);
        $provinceId = trim($param['province_id']);
        $cityId = intval($param['city_id']);
        $areaId = intval($param['area_id']);
        $address = trim($param['address']);
        $tel = trim($param['tel']);
        $classNum = intval($param['class_num']);
        $classDuration = intval($param['class_duration']);
        $classDate = trim($param['class_date']);
        $classIntro = trim($param['class_intro']);
        $isCoupon = intval($param['is_coupon']);
        $addUser = $userId;


        $images = Hera::upload("cover");

        $id = $param['id'] ? intval($param['id']) : 0;

        if (!$industryId)
            return message('请选择行业');

        if (!$levelId)
            return message('请选择等级');

        if (!$cycleId)
            return message('请选择周期');

        if (!$title)
            return message('请填写标题');

        if (!$subTitle)
            return message('请填写副标题');

        if (!$price)
            return message('请填写价格');

        if (!$provinceId)
            return message('请选择省份');

        if (!$cityId)
            return message('请选择市区');

        if (!$areaId)
            return message('请选择县区');

        if (!$address)
            return message('请填写详细地址');

        if (!$tel)
            return message('请填写联系电话');

        if (!Zeus::isValidMobile($tel))
            return message('请填写正确的联系电话');

        if (!$classNum)
            return message('请填写课时数');

        if (!$classDuration)
            return message('请填写课时长');

        if (!$classDate)
            return message('请填写上课时间');

        if (!$classIntro)
            return message('请填写课程详情');

        if (!$industryId)
            return message('请选择行业');

        if (!$isCoupon)
            return message('请选择课程是否可用优惠券');

        if (!$images && !$id)
            return message('请上传封面图');

        $data = [
            'industry_id'    => $industryId ,
            'level_id'       => $levelId ,
            'cycle_id'       => $cycleId ,
            'title'          => $title ,
            'sub_title'      => $subTitle ,
            'price'          => $price ,
            'province_id'    => $provinceId ,
            'city_id'        => $cityId ,
            'area_id'        => $areaId ,
            'address'        => $address ,
            'tel'            => $tel ,
            'cover'          => $images ,
            'top_img'        => $images ,
            'class_num'      => $classNum ,
            'class_duration' => $classDuration ,
            'class_date'     => $classDate ,
            'class_intro'    => '<p>' . $classIntro . '</p>' ,
            'is_coupon'      => $isCoupon ,
            'add_user'       => $addUser
        ];

        if($images){
            $data['cover'] = $images;
            $data['top_img'] = $images;
        }

        $trainMod = m('training');

        $info = $trainMod->getInfo($id);

        if ($id && ($info['add_user'] != $userId)) {
            return message('仅能修改自己发布的培训信息');
        }

        if ($id) {
            //审核未通过修改
            if ($info['status'] == 3)
                $data['status'] = 2;

            //如果已经通过了审核
            if ($info['status'] == 1)
                $data['status'] = 1;
        }

        //获取公司ID
        $data['company_id'] = m('company')->getCompanyIdByUserId($userId);

        $rs = $trainMod->edit($data , $id);

        if (!$rs)
            return message('系统繁忙，请稍候再试');

        //发送推送
        if ($data['status'] != 1) {
            if (!$id) {
                $title = '成功发布培训信息';
                $content = "您已成功发布培训信息【{$data['title']}】,我们会尽快为你审核处理";
            }
            else {
                $title = '成功修改培训信息';
                $content = "您已成功修改培训信息【{$data['title']}】,我们会尽快为你审核处理";
            }


            Zeus::sendMsg([
                'type'      => ['msg' , 'push'] ,
                'user_id'   => $userId ,
                'title'     => $title ,
                'content'   => $content ,
                'msg_type'  => 1 ,
                'user_type' => 1 ,
            ]);


        }

        return message('操作成功' , true);
    }


}