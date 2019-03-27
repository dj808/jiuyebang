<?php
	/**
	 * @todo    smarty自定义的组件
	 * @author Malcolm  (2018年04月09日)
	 */
function smarty_function_local($params) {
	$app = $params['app'];
	$act = $params['act'];
	$appFile = APP_ROOT."/{$app}.app.php";
	if (!is_file($appFile)) {
	    echo "local file {$appFile} not found";
	    return;
	}
	$pdata = array();
	foreach ($params as $name=>$value) {
	    $num = str_replace("param", "", $name);
	    $num = intval($num);
	    if ($num>0) {
	        $pdata[$num] = $value;
	    }
	}
	ksort($pdata);
	$pdata = array_values($pdata); 
	include_once($appFile);
	$appName = $app."App";
	$obj = new $appName();
	call_user_func_array(array($obj, $act), $pdata);
}
?>