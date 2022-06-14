<?php

/*
  Content Management System
  Copyright (C) 2008 by Systemantics, Bureau for Informatics

  Lutz Issler
  Mauerstr. 10-12
  41747 Viersen
  GERMANY

  Web:    www.systemantics.net
  Email:  mail@systemantics.net

  Permission granted to use the files associated with this
  website only on your webserver.
*/



function smarty_function_metadata($params, &$smarty) {
	$values = array(
		'Description' => 'description',
		'Keywords' => 'keywords',
		'Author' => 'author',
		'Copyright' => 'author',
		'Publisher' => 'publisher',
		'DC.CREATOR' => 'author',
		'DC.SUBJECT' => 'keywords',
		'DC.DESCRIPTION' => 'description',
		'DC.PUBLISHER' => 'publisher',
		'DC.CONTRIBUTORS' => 'contributors',
		'DC.LANGUAGE' => 'language',
		'DC.RIGHTS' => 'copyright',
	);
	$meta = '	<title>'.htmlspecialchars($params['meta']->title, false, 'utf-8')."</title>\n";
	foreach ($values as $name=>$value) {
		if ($params['meta']->{$value}) {
			$meta .= '	<meta name="'.$name.'" content="'.htmlspecialchars(strip_tags($params['meta']->{$value}), false, 'utf-8').'" />'."\n";
		}
	}
	return $meta;
}
