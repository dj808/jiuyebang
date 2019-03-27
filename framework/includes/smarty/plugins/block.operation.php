<?php   
function smarty_block_operation($params, $content, $template, &$repeat) { 
	if ($repeat==false) {
		$name = $params['name'];
		$allOperation  = $template->tpl_vars['allOperation']['value'];
		$adminOperation = $template->tpl_vars['adminOperation']['value'];
		if (in_array($name, $allOperation) && !in_array($name, $adminOperation)) {
			return;
		}
		echo $content;
	}

} ?>