<?php

/*
  Get the first N words
  Copyright (C) 2009 by Systemantics, Bureau for Informatics

  Lutz Issler
  Mauerstr. 10-12
  47877 Willich
  GERMANY

  Web:    www.systemantics.net
  Email:  mail@systemantics.net

  This file is free to copy and use. No copyright notice
  must be maintained.
*/



define('__HTML_EM_OPEN', '##__HTML__EM__OPEN__##');
define('__HTML_EM_CLOSE', '##__HTML__EM__CLOSE__##');

function smarty_modifier_abbreviate($string, $length, $suffix = ' ...') {
	$string_replaced = str_replace(array('<i>', '<em>'), __HTML_EM_OPEN, $string);
	$string_replaced = str_replace(array('</i>', '</em>'), __HTML_EM_CLOSE, $string_replaced);
	$string_replaced = strip_tags(html_entity_decode($string_replaced, ENT_QUOTES, 'utf-8'));
	$shortened_replaced = implode(' ', array_slice(explode(' ', $string_replaced), 0, $length));
	$shortened = str_replace(__HTML_EM_OPEN, '<em>', $shortened_replaced, $emOpenCount);
	$shortened = str_replace(__HTML_EM_CLOSE, '</em>', $shortened, $emCloseCount);
	while ($emCloseCount<$emOpenCount) {
		$shortened = $shortened.'</em>';
		$emCloseCount = $emCloseCount+1;
	}
	return $shortened_replaced==$string_replaced ? $string : $shortened.$suffix;
}

?>