<?php
	
	
	/**
	 * 工作地点 队列消费者
	 * Created by Malcolm.
	 * Date: 2018年03月22日
	 */
	class GpsManageApp extends BaseScriptApp {
		
		
		
		public function handle($msg,$queueName) {
			$pid = getmypid();
			
			//拆分参数
			$type = $msg['type'];
			$typeId = $msg['typeId'];
			
			
			if(!$type || !$typeId){
				$log = '队列进程：'.$pid.' 执行失败，消息体为：'.json_encode($msg);
				Zeus::setRabbitMQLog(2,2,$log,$queueName);
				
				return false;
			}
			
			if('job'==$type)
				$jobMod = m('job');
			else
				$jobMod = m('job');
			
			
			//取出职位信息
			$jobInfo = $jobMod->getInfo($typeId);
			
			//引入高德服务
			import('gaode/service.lib');
			$gaode = new Service();
			
			$cityName = m('city')->getCityName($jobInfo['dist_id']);
			$address = $cityName.$jobInfo['address'];
			$addressInfo = $gaode->getGpsByAddress($address);
			
			if($addressInfo['status']==1 && $addressInfo['geocodes'][0]['location']){
				$gps = explode(',',$addressInfo['geocodes'][0]['location']);
				
				if($jobInfo['lng'] != $gps[0] || $jobInfo['lat'] != $gps[1] ){      //如果有修改 则编辑
					$data = [
						'lng' => $gps[0],
						'lat' => $gps[1],
					];
					
					$rs = $jobMod->edit($data,$typeId);
					
					if(!$rs){
						$log = '队列进程：'.$pid.' 执行失败，消息体为：'.json_encode($msg);
						Zeus::setRabbitMQLog(2,2,$log,$queueName);
						
						return false;
						
					}
				}
			}else{
				$log = '队列进程：'.$pid.' 执行失败，获取的地理信息为：'.json_encode($addressInfo);
				Zeus::setRabbitMQLog(2,2,$log,$queueName);
				
				return false;
			}
			
			$log = '队列进程：'.$pid.' 执行成功，消息体为：'.json_encode($msg);
			Zeus::setRabbitMQLog(2,1,$log,$queueName);
			
			return true;
		}
		
		
		
	}