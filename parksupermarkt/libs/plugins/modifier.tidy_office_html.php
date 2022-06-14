<?php

/*
  Tidy Office HTML
  Copyright (C) 2012 by Systemantics, Bureau for Informatics

  Systemantics GmbH
  Alte Poststr. 38
  47877 Willich
  GERMANY

  Web:    www.systemantics.net
  Email:  hello@systemantics.net

  This file is free to copy and use. No copyright notice
  must be maintained.
*/



// Regexps courtesy of 1st class media
// http://www.1stclassmedia.co.uk/developers/clean-ms-word-formatting.php
function smarty_modifier_tidy_office_html($str) {
	$replacements = array(
		'/<!--.*?-->/s' => '',
		'/<o:p>\s*<\/o:p>/s' => '',
		'/<o:p>.*?<\/o:p>/s' => "&nbsp;",
		'/\s*mso-[^:]+:[^;"]+;?/i' => '',
		'/\s*MARGIN: 0cm 0cm 0pt\s*;/i' => '',
		'/\s*MARGIN: 0cm 0cm 0pt\s*"/i' => '',
		'/\s*TEXT-INDENT: 0cm\s*;/i' => '',
		'/\s*TEXT-INDENT: 0cm\s*"/i' => '',
		'/\s*TEXT-ALIGN: [^\s;]+;?"/i' => '',
		'/\s*PAGE-BREAK-BEFORE: [^\s;]+;?"/i' => '',
		'/\s*FONT-VARIANT: [^\s;]+;?"/i' => '',
		'/\s*tab-stops:[^;"]*;?/i' => '',
		'/\s*tab-stops:[^"]*/i' => '',
		'/\s*face="[^"]*"/i' => '',
		'/\s*face=[^ >]*/i' => '',
		'/\s*FONT-FAMILY:[^;"]*;?/i' => '',
		'/<(\w[^>]*) class=([^ |>]*)([^>]*)/i' => "<$1$3",
		'/<(\w[^>]*) style="([^\"]*)"([^>]*)/i' => "<$1$3",
		'/\s*style="\s*"/i' => '',
		'/<SPAN\s*[^>]*>\s*&nbsp;\s*<\/SPAN>/i' => '&nbsp;',
		'/<SPAN\s*[^>]*><\/SPAN>/i' => '',
		'/<(\w[^>]*) lang=([^ |>]*)([^>]*)/i' => "<$1$3",
		'/<SPAN\s*>(.*?)<\/SPAN>/i' => '$1',
		'/<FONT\s*>(.*?)<\/FONT>/i' => '$1',
		':<p>&nbsp;</p>:i' => '',
		'/<\\?\?xml[^>]*>/i' => '',
		'/<\/?\w+:[^>]*>/i' => '',
		'/<([^\s>]+)[^>]*>\s*<\/\1>/s' => '',
	);
	foreach ($replacements as $pattern => $replacement) {
		$str = preg_replace($pattern, $replacement, $str);
	}
	return $str;
}

?>