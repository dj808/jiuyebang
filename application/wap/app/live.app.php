<?php

/**
 *  直播中转控制器
 * Created by Malcolm.
 * Date: 2018/5/19  12:31
 */
class LiveApp extends FrontendApp{
    public $userId;

    public function __construct() {
        parent::__construct();

        $this->userId = $this->params['user_id'];
    }


    /**
     * @todo    投票跳转
     * @author Malcolm  (2018年05月19日)
     */
    public function index(){
        $this->render('index.html');
        /*$url = "https://zhibo.chaoxing.com/weblive?streamName=LIVENEWBS07ziR6&vdoid=121848kh845";

        header("Location:$url");

        exit;*/
    }






}