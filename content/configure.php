<?php

/*
  Content Management System

  Copyright (C) 2006–2008 Systemantics

  Systemantics,
  Bureau for Informatics
  Mauerstr. 10-12
  52064 Aachen
  GERMANY

  Web:    www.systemantics.net
  Email:  mail@systemantics.net

  This file is NOT free software. Modification or usage of this file without
  buying an appropriate license from Systemantics breaks international
  copyright laws.
*/



require_once('config.inc.php');
require_once('constants.inc.php');



if ($_REQUEST[PARAM_SOURCE]) {
	$fn = $_REQUEST[PARAM_SOURCE];
	if ($_REQUEST['type']!='') {
		$ct = $_REQUEST['type'];
	} else if (function_exists('mime_content_type')) {
		$ct = mime_content_type($fn);
	} else {
		$ct = 'text/css';
	}
	header('Content-Type: '.$ct);
	$config = new config();
	echo preg_replace_callback(
		'/%(.+)%/U',
		function ($matches) {
			return $GLOBALS["config"]->value(constant($matches[1]));
		},
		implode('', file($fn))
	);
}



?>