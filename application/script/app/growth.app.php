<?php
	
	/**
	 * 成长值维护脚本
	 * Created by Malcolm.
	 * Date: 2018/3/9  11:07
	 */
	class GrowthApp extends BaseScriptApp {
		public $userMod,$userDigitalRelMod;
		public function __construct () {
			$this->userMod = m('user');
			false&&$this->userMod = new UserMod();
			
			$this->userDigitalRelMod = m('userDigitalRel');
			false&&$this->userDigitalRelMod = new UserDigitalRelMod();
		}
		
		
		public function handle($msg,$queueName) {
			$pid = getmypid();
			
			$userId = $msg['user_id'];
			$num = $msg['num'];
			$type = $msg['type'];
			
			$todo = $msg['todo'];
			
			if(!$userId || !$num || !$type || !$todo){
				$log = '队列进程：'.$pid.' 执行失败，消息体为：'.json_encode($msg);
				Zeus::setRabbitMQLog(2,2,$log,$queueName);
				
				return false;
			}
			
			if($todo=='growth'){
				$rs = $this->manageGrowth($userId,$num,$type);
				
				if(!$rs){
					$log = '队列进程：'.$pid.' 执行失败，消息体为：'.json_encode($msg);
					Zeus::setRabbitMQLog(2,2,$log,$queueName);
				}
			}
			
			if ($todo=='integral'){
				//冗余参数
				$shoppingType = $msg['shopping_type'];
				$shoppingTypeId = $msg['shopping_type_id'];
				
				$rs = $this->manageIntegral($userId,$num,$type,$shoppingType,$shoppingTypeId);
				
				if(!$rs){
					$log = '队列进程：'.$pid.' 执行失败，消息体为：'.json_encode($msg);
					Zeus::setRabbitMQLog(2,2,$log,$queueName);
				}
				
			}
			
			$log = '队列进程：'.$pid.' 执行成功，消息体为：'.json_encode($msg);
			Zeus::setRabbitMQLog(2,1,$log,$queueName);
			return true;
		}
		
		
		
		/**
		 * @todo    维护成长值（1签到 2兼职 3培训 4邀请 5互助）
		 * @author Malcolm  (2018年03月09日)
		 */
		public function manageGrowth($userId,$num,$type=1){
			//获取用户信息
			$userInfo = $this->userMod->getInfo($userId);
			
			$this->userMod->transStart();
			//维护用户主表
			$userData = [
				'growth' =>$userInfo['growth']+$num,
			];
			
			switch ($type){
				case 1:
					$befor =$userInfo['growth_sign'];
					$after = $userData['growth_sign'] = $userInfo['growth_sign']+$num;
					$msg = " 您已成功完成签到，获赠成长值:{$num}，总签到成长值为：{$after} ";
					break;
				case 2:
					$befor =$userInfo['growth_job'];
					$after = $userData['growth_job'] = $userInfo['growth_job']+$num;
					$msg = " 您已成功完成兼职任务，获赠成长值:{$num}，总兼职成长值为：{$after} ";
					break;
				case 3:
					$befor =$userInfo['growth_train'];
					$after = $userData['growth_train'] = $userInfo['growth_train']+$num;
					$msg = " 您已成功完成培训任务，获赠成长值:{$num}，总培训成长值为：{$after} ";
					break;
				case 4:
					$befor =$userInfo['growth_invite'];
					$after = $userData['growth_invite'] = $userInfo['growth_invite']+$num;
					$msg = " 您已成功邀请好友注册，获赠成长值:{$num}，总邀请成长值为：{$after} ";
					break;
				case 5:
					$befor =$userInfo['growth_help'];
					$after = $userData['growth_help'] = $userInfo['growth_help']+$num;
					$msg = " 您已成功完成互助任务，获赠成长值:{$num}，总互助成长值为：{$after} ";
					break;
					
				default:
					$befor =$userInfo['growth_sign'];
					$after = $userData['growth_sign'] = $userInfo['growth_sign']+$num;
					$msg = " 您已成功签到，获赠成长值:{$num}，总签到成长值为：{$after} ";
			}
			
			//维护会员等级
			if($userData['growth']>=0 && $userData['growth']<=1000){
				$userData['vip_level'] = 1;
			}elseif ($userData['growth']>=1001 && $userData['growth']<=3000){
				$userData['vip_level'] = 2;
			}elseif ($userData['growth']>=3001 && $userData['growth']<=6000){
				$userData['vip_level'] = 3;
			}elseif ($userData['growth']>=60001 && $userData['growth']<=9000){
				$userData['vip_level'] = 4;
			}elseif ($userData['growth']>=9001 && $userData['growth']<=12000){
				$userData['vip_level'] = 5;
			}elseif ($userData['growth']>=12001 && $userData['growth']<=15000){
				$userData['vip_level'] = 6;
			}elseif ($userData['growth']>=15001 ) {
				$userData['vip_level'] = 7;
			}
			
			$rs = $this->userMod->edit($userData,$userId);
			if(!$rs){
				$this->userMod->transBack();
				return false;
			}
			
			//维护记录表
			$relData = [
				'user_id' => $userId,
				'classify' => 2,
				'type' => $type,
				'num' => $num,
				'befor' => $befor,
				'after' => $after,
			];
			
			$rs = $this->userDigitalRelMod->edit($relData);
			if(!$rs){
				$this->userMod->transBack();
				return false;
			}
			
			
			$this->userMod->transCommit();
			
			//推送提醒
			Zeus::sendMsg([
				'type' =>['msg','push'],
				'user_id' =>$userId,
				'title' =>'成长值变更通知',
				'content' =>$msg,
				'msg_type' =>1,
				'user_type' =>1,
			]);
			
			return true;
			
		}
		
		
		/**
		 * @todo    维护积分（1消费获得 2邀请 3积分消费）
		 * @author Malcolm  (2018年03月16日)
		 */
		public function manageIntegral($userId,$num,$type=1,$shoppingType=0,$shoppingTypeId=0){
			//获取用户信息
			$userInfo = $this->userMod->getInfo($userId);
			
			$this->userMod->transStart();
			$total = $userInfo['integral'] + $num;
			//维护用户主表
			$userData = [
				'integral' => $total
			];
			
			$rs = $this->userMod->edit($userData,$userId);
			if(!$rs){
				$this->userMod->transBack();
				return false;
			}
			
			
			//维护记录表
			$relData = [
				'user_id' => $userId,
				'classify' => 1,
				'type' => $type,
				'num' => $num,
				'befor' => $userInfo['integral'],
				'after' => $total,
				'shopping_type' => $shoppingType,
				'shopping_type_id' => $shoppingTypeId,
			];
			
			$rs = $this->userDigitalRelMod->edit($relData);
			if(!$rs){
				$this->userMod->transBack();
				return false;
			}
			
			switch ($type){
				case 1:
					$msg = " 您已成功获取积分：{$num}，当前总积分为：{$total}。 ";
					break;
					
				case 2:
					$msg = " 您已成功邀请好友注册，获取积分：{$num}，当前总积分为：{$total}。 ";
					break;
					
				case 3:
					$msg = " 您已使用积分支付，已成功扣除：{$num}，当前总积分为：{$total}。 ";
					break;
					
				default:
					$msg = " 您已成功获取积分：{$num}，当前总积分为：{$total}。 ";
					
			}
			
			$this->userMod->transCommit();
			
			//推送提醒
			Zeus::sendMsg([
				'type' =>['msg','push'],
				'user_id' =>$userId,
				'title' =>'积分变更通知',
				'content' =>$msg,
				'msg_type' =>1,
				'user_type' =>1,
			]);
			
			return true;
		}
		
		
		
		
	}