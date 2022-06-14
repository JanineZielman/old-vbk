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



function smarty_modifier_abbreviate_cn($string, $length, $suffix = ' ...') {
	$string = strip_tags(html_entity_decode($string, ENT_QUOTES, 'utf-8'));
	$shortened = mb_substr($string, 0, $length);
	$i = $length;
	while ($i<mb_strlen($string) && !in_array(mb_substr($string, $i, 1), array(' ', '。', '，', '、', '一'))) {
		$shortened .= mb_substr($string, $i, 1);
		$i++;
	}
	return $shortened==$string ? $string : $shortened.$suffix;
}

?>