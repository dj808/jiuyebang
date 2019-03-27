<?php

/**
 *  投票中转控制器
 * Created by Malcolm.
 * Date: 2018/5/19  12:31
 */
class VoteApp extends FrontendApp{
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
        if(!$this->userId)
            $this->userId = 0;

        //处理连接
        $url = 'https://tp.wjx.top/m/23956386.aspx';

        $sojumpparm = 'jyb_vote';

        $userId = $this->userId.','.$sojumpparm;

        $url = $url.'?sojumpparm='.$userId;



        //判断是否投票
        //设置超时参数
        $opts=array(
            "http"=>array(
                "method"=>"GET",
                "timeout"=>3
            ),
        );
        ////创建数据流上下文
        $context = stream_context_create($opts);

        //$url请求的地址，例如：

        $result =file_get_contents($url, false, $context);

        if(!strstr($result,'很抱歉'))
            header("Location:$url");
        else
            $this->result();

        exit;
    }


    /**
     * @todo    显示结果页
     * @author Malcolm  (2018年05月19日)
     */
    public function result(){
        $url = 'https://tp.wjx.top/wjx/join/completemobile2.aspx?activity=23956386&joinactivity=101557245744&jidx=59&jpm=2';


        $result =file_get_contents($url);


        $data = [
            'id="divdsc">' => 'id="divdsc" style="color: red" >',
            'id=\'divTpResult\' style=\'display:none;\'' => 'id=\'divTpResult\' ',
            'alert(\'非常抱歉，您没有访问资格！\');' => ' ',
            'window.location="http://www.891jyb.com";' => '',

            '问卷星' => '',
            '提供技术支持' => '',
            '查看投票结果' => '',
            'style=\'padding-left:80px;padding-top:3px;\'' => '',
            '</style>' => 'body{padding: 0px 15px}  a , img { display: none; } </style>',
        ];

        $result = strtr($result,$data);

        $this->assign('result',$result);

        $this->render('index.html');
    }


    /**
     * @todo    投票已经截至
     * @author Malcolm  (2018年05月21日)
     */
    public function end(){

    }



}