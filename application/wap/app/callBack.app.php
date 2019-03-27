<?php
/**
 *
 * Created by Malcolm
 * Date: 2018/5/19 13:52
 */

/**
 *  投票回调
 * Created by Malcolm.
 * Date: 2018/5/19  13:52
 */
class CallBackApp extends FrontendApp{
    public function __construct() {
        parent::__construct();

    }


    /**
     * @todo    回调处理
     * @author Malcolm  (2018年05月19日)
     */
    public function index() {
        $data = @file_get_contents("php://input");

        $data = json_decode($data,true);

        Zeus::push('vote',$data);

        /*$tmp = explode(',',$data['sojumpparm']);

        $userId = $tmp[0];

        $form = $tmp[1];


        $data = [
            'user_id' => $userId,
            'activity' => $data['activity'],
            'submittime' => $data['submittime'],
            'content' => json_encode($data),
            'add_time' => time(),
        ];

        $rs = m('vote')->edit($data);*/

        echo 'OK';
        exit();
    }

}