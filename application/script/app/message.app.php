<?php
	
	/**
	 * 消息队列消费者
	 * Created by Malcolm.
	 * Date: 2017/12/21  09:48
	 */
	class MessageApp extends BaseScriptApp {
		
		
		
		public function handle($msg,$queueName){
			$pid = getmypid();
			
			//拆分参数
			$type = $msg['type'];
			
			$content = $msg['content'];
			
			$title = $msg['title'];
			
			$mobile = $msg['mobile'];
			
			$msg_type = $msg['msg_type']?$msg['msg_type']:1;
			
			//推送跳转类型
			if(!$msg['extras']){
				$extras = [
					'is_need_renew_auth' =>'2',
				];
			}else{
				$extras = $msg['extras'];
			}
			
			if(!$msg['user_id'])
				$msg['user_id'] = m('user')->getUserIdByMobile($mobile);
			
			
			if(!$type || !$content || !$mobile ) {
				$log = '队列进程：'.$pid.' 执行失败，消息体为：'.json_encode($msg);
				Zeus::setRabbitMQLog(2,2,$log,$queueName);
				
				return false;
			}
			

			//如果发送短信
			if(in_array('sms',$type)){
				Zeus::sendSms($mobile, $content);
			}
			
			//如果有推送
			if(in_array('push',$type)){
				//获取用户推送别名
                if(!$msg['device_id'])
				    $deviceId = m('user')->getDeviceIdByMobile($mobile);
                else
                    $deviceId = $msg['device_id'];
				
				import("jpush/JPush");
				$client = new JPush("11dd2beba30978b536b11606", "e87e6e95a4a536b2501a422d");
				
				if($deviceId){
					$result = $client->push()->setPlatform(array('ios', 'android'))
						->addAlias($deviceId)
						->setMessage($content, $title, null, $extras)
						->setOptions(null, 86400, null, true)
						->send();
				}
				
			}
			
			//如果有站内信
			if(in_array('msg',$type)){
				//获取用户ID
				
				$msgData = [
					'type' =>$msg['user_type']?$msg['user_type']:1,
					'msg_type' =>$msg_type,
					'user_id' =>$msg['user_id'],
					'title' =>$title,
					'content' =>$content
				];
				
				$rs = m('systemMessage')->edit($msgData);
				
				if(!$rs){
					$log = '队列进程：'.$pid.' 执行失败，消息体为：'.json_encode($msg);
					Zeus::setRabbitMQLog(2,2,$log,$queueName);
				}
				
				$msgRel = [
					'user_id' =>$msg['user_id'],
					'message_id' =>$rs,
					'is_read' =>2
				];
				
				$rs = m('systemMessageRelation')->edit($msgRel);
				
				if(!$rs){
					$log = '队列进程：'.$pid.' 执行失败，消息体为：'.json_encode($msg);
					Zeus::setRabbitMQLog(2,2,$log,$queueName);
				}
			}
			
			
			$log = '队列进程：'.$pid.' 执行成功，消息体为：'.json_encode($msg);
			Zeus::setRabbitMQLog(2,1,$log,$queueName);
			
			return true;
		}
		
	}