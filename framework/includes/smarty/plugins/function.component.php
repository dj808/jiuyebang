<?php
/**
 * @author dyg
 * smarty自定义的组件
 * @param array $params
 */
function smarty_function_component($params) {
	if (empty ( $params ['file'] )) {
		$params['file'] = 'page';//默认是分页组件
	}
	$content = array ();
	if (! empty ( $params ['data'] )) {
		$content = $params ['data'];
	}
	component ( $params ['file'], $content );
}
?>