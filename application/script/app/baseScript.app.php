<?php
/**
 * 脚本基类  
 */
class BaseScriptApp extends BaseApp{
 
 
	/**
	 * @todo    队列系统处理
	 * @author Malcolm  (2017年08月23日)
	 */
	public function queue(){
	    $queueName = APP;
		import('/rabbitMq/rabbitMQ');
		
		$redis = cache_server()->server;
		$timeFlag = time();
		START_CONNECTION:
		//检测是否关闭消费进程
		$status = $redis->get(DB_PREFIX.'consume_status');
		if(2==$status){
			echo '安全退出消费进程  PID： '.getmypid().'--'.date('Y-m-d H:i:s',time())."\n";
			exit();
		}
		
		//echo '开始连接rabbit服务--'.date('Y-m-d H:i:s',time())."\n";
		$queue = new RabbitMq($queueName);
		$cnt = 0;
		
		while (1) {
			$now = time();
			
			//避免mysql链接丢失，每小时重新连接一下
			if($now-$timeFlag>=1*3600){
				m("ping")->execute("SELECT 1");
				echo '避免mysql链接丢失，每小时重新连接一下--'.date('Y-m-d H:i:s',time())."\n";
				$timeFlag = time();
			}
			
			
			list($ack,$data) = $queue->get();
			if(!$data){
				$cnt++;
				if($cnt > 20){
					$queue->close();
					goto START_CONNECTION;
				}
				//echo "no data:$cnt --".date('Y-m-d H:i:s',time())." \n";
				usleep(0.5*1000000);
				continue;
			}
			
			//逻辑处理
			echo "PID： ".getmypid()."start work in ".date('Y-m-d H:i:s',time())." \n";
			
			$rs = $this->handle(json_decode($data,true),$queueName);
			
			//确认消耗
			if(!$rs)
				echo "the work is error ".date('Y-m-d H:i:s',time())." \n\n\n\n";
			
			echo "end work in ".date('Y-m-d H:i:s',time())." \n\n\n\n";
			$ack();
		}
		
		
	}
 
 
}