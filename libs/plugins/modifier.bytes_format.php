<?php

/*
  String indention
  Copyright (C) 2008 by Systemantics, Bureau for Informatics

  Lutz Issler
  Mauerstr. 10-12
  47877 Willich
  GERMANY

  Web:    www.systemantics.net
  Email:  mail@systemantics.net

  This file is free to copy and use. No copyright notice
  must be maintained.
*/



function smarty_modifier_bytes_format($string) {
	$bytes = intval($string);
	$units = array('B', 'KB', 'MB', 'GB', 'TB');
	$level = 0;
	while ($bytes>=1000 && $level<count($units)-1) {
		$bytes = $bytes/1024;
		$level++;
	}
	return round($bytes).' '.$units[$level];
}

?>