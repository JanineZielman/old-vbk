<?php

/*
  Content Management System
  Copyright (C) 2009 by Systemantics, Bureau for Informatics

  Lutz Issler
  Mauerstr. 10-12
  47877 Willich
  GERMANY

  Web:    www.systemantics.net
  Email:  mail@systemantics.net

  Permission granted to use the files associated with this
  website only on your webserver.
*/



function smarty_function_html_file($params, &$smarty) {
	$value = trim($smarty->getTemplateVars($params['id']));
	$missing = $smarty->getTemplateVars($params['form'].'_missing');
	if (is_array($missing) && in_array($params['id'], $missing)) {
		$params['class'] .= ' missing';
	}
	$html = '<dt><label for="'.$params['id'].'">'.$params['label'].($params['required'] ? ' *' : '').'</label></dt><dd><input type="file" id="'.$params['id'].'"';
	if (trim($params['class'])!='') {
		$html .= ' class="'.trim($params['class']).'"';
	}
	return $html.' name="'.$params['id'].'" /></dd>';
}

?>