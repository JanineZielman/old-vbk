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



function smarty_function_html_checkbox($params, &$smarty) {
	$value = trim($smarty->getTemplateVars($params['id']));
	if (is_array($_SESSION['missing']) && in_array($params['id'], $_SESSION['missing'])) {
		$params['class'] .= ' missing';
	}
	$html = '<div class="checkbox"><label><input type="checkbox"';
	if (trim($params['class'])!='') {
		$html .= ' class="'.trim($params['class']).'"';
	}
	$html .= ' name="'.$params['id'].'" value="1"';
	if ($value=='1') {
		$html .= ' checked="checked"';
	}
	return $html.' />'.$params['label'].'</label></div>';
}

?>