<?php

/*
  Trim empty tags from output
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



function smarty_outputfilter_trimemptytags($source, &$smarty) {
	return preg_replace(
		array(
			'|<h1(\s[^>]*)?>\s*</h1>|',
			'|<h2(\s[^>]*)?>\s*</h2>|',
			'|<h3(\s[^>]*)?>\s*</h3>|',
			'|<ul(\s[^>]*)?>\s*</ul>|',
			':<p(\s[^>]*)?>(&nbsp;|\s)*</p>:',
			':<p(\s[^>]*)?><!--.*--></p>:',
			'|<b(\s[^>]*)?>\s*</b>|',
			'|<i(\s[^>]*)?>\s*</i>|',
			'|<u(\s[^>]*)?>\s*</u>|',
			'|<!--\[if gte mso.*<!\[endif\]-->|sU',
		),
		'',
		$source
	);
}

?>
