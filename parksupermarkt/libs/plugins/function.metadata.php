<?php

/*
  Content Management System
  Copyright (C) 2008 by Systemantics, Bureau for Informatics

  Lutz Issler
  Mauerstr. 10-12
  47877 Willich
  GERMANY

  Web:    www.systemantics.net
  Email:  mail@systemantics.net

  Permission granted to use the files associated with this
  website only on your webserver.
*/



function smarty_function_metadata($params, &$smarty) {
	return '	<title>'.htmlentities($params['meta']->title, false, 'utf-8').'</title>
	<meta name="Description" content="'.htmlentities($params['meta']->description, false, 'utf-8').'" />
	<meta name="Keywords" content="'.htmlentities($params['meta']->keywords, false, 'utf-8').'" />
	<meta name="Author" content="'.htmlentities($params['meta']->author, false, 'utf-8').'" />
	<meta name="Copyright" content="'.htmlentities($params['meta']->author, false, 'utf-8').'" />
	<meta name="Publisher" content="'.htmlentities($params['meta']->publisher, false, 'utf-8').'" />
	<meta name="DC.TITLE" content="'.htmlentities($params['meta']->title, false, 'utf-8').'" />
	<meta name="DC.CREATOR" content="'.htmlentities($params['meta']->author, false, 'utf-8').'" />
	<meta name="DC.SUBJECT" content="'.htmlentities($params['meta']->keywords, false, 'utf-8').'" />
	<meta name="DC.DESCRIPTION" content="'.htmlentities($params['meta']->description, false, 'utf-8').'" />
	<meta name="DC.PUBLISHER" content="'.htmlentities($params['meta']->publisher, false, 'utf-8').'" />
	<meta name="DC.CONTRIBUTORS" content="'.htmlentities($params['meta']->contributors, false, 'utf-8').'" />
	<meta name="DC.DATE" content="'.$params['meta']->date.'" />
	<meta name="DC.TYPE" content="Interactive Resource" />
	<meta name="DC.FORMAT" content="text/html" />
	<meta name="DC.IDENTIFIER" content="'.$params['meta']->identifier.'" />
	<meta name="DC.LANGUAGE" content="'.$params['meta']->language.'" />
	<meta name="DC.RIGHTS" content="'.htmlentities($params['meta']->copyright, false, 'utf-8').'" />';
}

?>