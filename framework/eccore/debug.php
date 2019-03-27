<?php 
/***** debug function  start ****/
/** 
 * 微信调试agent头判断
 * @author 刘小祥
 * @date 2016年3月22日
 */
function is_weixin_debug() {
    return strpos($_SERVER['HTTP_USER_AGENT'], "weixin_debug_mode");
}
/**
 * 默认调试agent头判断
 * @author 刘小祥
 * @date 2016年3月22日
 */
function is_debug() {
    return strpos($_SERVER['HTTP_USER_AGENT'], 'iart_debug_mode');
}
/**
 * debug模式下打印并退出
 * @author 刘小祥
 * @date 2016年3月22日
 */
function debug_printd($data) {
    is_debug() && printd($data);
}
/**
 * debug模式下打印(不退出)
 * @author 刘小祥
 * @date 2016年3月22日
 */
function debug_printc($data) {
    is_debug() && printc($data);
}

/**
 * 打印并退出
 * @author 刘小祥
 * @date 2016年3月22日
 */
function printd($data){
	if (php_sapi_name()=="cli") {
		print_r($data);
		echo "\n";
		exit();
	}
	@header("Content-type:text/html;charset=utf-8");
	if (false && IS_POST) {
		if ($data) {
			print_r($data);
		}else{
			var_dump($data);
		}
		exit();
	}
	$info = debug_backtrace();
	echo "<div style='background-color:#EEEEEE;border:solid 1px gray;'><div style='padding-left:10px;line-height:25px;background-color:gray;color:white;font-size:14px;font-weight:bold;'>";
	echo "{$info[0]['file']}:{$info[0]['line']}\n";
	echo "</div><div style='padding-left:10px;'>";
	echo "<pre>";
	if($data){
		print_r($data);
	}else{
		var_dump($data);
		echo "use memory ".round(memory_get_usage()/1024);
	}
	echo "</pre><br>";
	echo "</div></div>";
	die;
}

/**
 * 打印并退出
 * @author 刘小祥
 * @date 2016年3月22日
 */
function printc($data){
	if (php_sapi_name()=="cli") {
		print_r($data);
		echo "\n";
	}
	header("Content-type:text/html;charset=utf-8");
	if (false && IS_POST) {
		if ($data) {
			print_r($data);
		}else{
			var_dump($data);
		}
		return;
	}
	$info = debug_backtrace();
	echo "<div style='background-color:#EEEEEE;border:solid 1px gray;'><div style='padding-left:10px;line-height:25px;background-color:gray;color:white;font-size:14px;font-weight:bold;'>";
	echo "{$info[0]['file']}:{$info[0]['line']}\n";
	echo "</div><div style='padding-left:10px;'>";
	echo "<pre>";
	if($data){
		print_r($data);
	}else{
		var_dump($data);
	}
	echo "</pre><br>";
	echo "</div></div>";
}

$GLOBALS['timer'] = microtime();
function runtime() {
	$now = microtime();
	list($old_mm,$old_sec) = explode(" ", $GLOBALS['timer']);
	list($now_mm,$now_sec) = explode(" ", $now );
	$time = $now_sec-$old_sec+$now_mm-$old_mm;
	$GLOBALS['timer']= $now;
	return $time; 
} 

function debug_error($errno, $errstr, $errfile, $errline) {
	if ($errno!=E_NOTICE&&$errno!=E_DEPRECATED&&$errno!=E_STRICT) {
		echo "<div style='background-color:#F8F8FF;border:solid 1px gray;'>";
		echo "<div style='padding-left:10px;line-height:25px;background-color:#6CA6CD;color:white;fon-weight:bold;line-height:40px;font-size:25px;font-weight:bold;font-famliy:微软雅黑;'>";
		echo "<b>Error occurs:</b><br />";
		echo "</div>";
		echo "<div style=\"line-height:25px;padding-left:10px;color:gray\"><b> [$errno] $errstr</b></div>";
		$list = debug_backtrace(); 
		foreach ($list as $index=>$row) {
			if ($index) {  
				$row['args'] = (array) $row['args'];
				$args = implode(", ",$row['args']);
				echo "<div style='color:gray;line-height:18px;padding-left:10px;padding-bottom:1px;'>#{$index} {$row['file']}({$row['line']}): {$row['class']}{$row['type']}{$row['function']}({$args})</div>";
			}
		}
		echo "</div>";
		die();
	} 
}

function  sqlDebug($sql, $stdCode, $errCode, $msg ) {
	log::jsonInfo($sql);
    if ($errCode==0) {
        printc($sql);
        return;
    }
    printc(func_get_args());
    $list = debug_backtrace();
    array_shift($list);
    $trace = ""; 
    foreach ($list as $key=>$row) {
        $index = $key+1;
        $objectName = $row['class'] ? $row['class']."->":"";
        $trace .= "#{$index} ".$row['file'].":".$row['line']." ".$objectName.$row['function']."\n";
    }
    printc($trace);
    exit();
}
	
	
	/**
	 * @todo 发送消息日志
	 * @author zhouquan <zhouquan@quyishu.com> (2016年6月21日)
	 */
	function writeMessageLog($content, $logFile) {
		$logPath = ROOT_PATH . "/log/{$logFile}/" . date('Ymd') . '/';
		
		if(!is_dir($logPath)) {
			mkdir($logPath, 0777, true);
		}
		
		$logFileName = $logPath . date("H").'.log';
		
		$str = '['.date("Y-m-d H:i:s").']'.$content."\r\n";
		
		file_put_contents($logFileName, $str, FILE_APPEND);
	}