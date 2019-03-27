<?php


/**
 *  投票队列消费者
 * Created by Malcolm.
 * Date: 2018/5/19  17:09
 */
class VoteApp extends BaseScriptApp {



    public function handle($msg,$queueName) {
        $pid = getmypid();

        //拆分参数

        $tmp = $msg['sojumpparm'];

        $tmp = explode(',',$tmp);

        $userId = $tmp[0];

        $form = $tmp[1];

        if(!$form){
            $log = '队列进程：'.$pid.' 无效消息丢弃，消息体为：'.json_encode($msg);
            Zeus::setRabbitMQLog(2,2,$log,$queueName);

            return false;
        }


        //入库记录
        $data = [
            'user_id' => $userId,
            'activity' => $msg['activity'],
            'submittime' => $msg['submittime'],
            'content' => json_encode($msg),
            'add_time' => time(),
        ];

        $rs = m('vote')->edit($data);

        if(!$rs){
            $log = '队列进程：'.$pid.' 执行失败，消息体为：'.json_encode($msg);
            Zeus::setRabbitMQLog(2,2,$log,$queueName);
        }


        //发送成功推送
        Zeus::sendMsg([
            'type' =>['msg','push'],
            'user_id' =>$userId,
            'title' =>'投票成功通知',
            'content' =>'您已成功投票，感谢您的参与，活动结束后，我们会按参与顺序发放大礼包！',
            'msg_type' =>1,
            'user_type' =>1,
        ]);


        $log = '队列进程：'.$pid.' 执行成功，消息体为：'.json_encode($msg);
        Zeus::setRabbitMQLog(2,1,$log,$queueName);
        return true;
    }

}