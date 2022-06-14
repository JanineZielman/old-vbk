<?php

/*
  van Bergen Kolpa
  Copyright (C) 2006-2010 by Systemantics, Bureau for Informatics

  Systemantics GmbH
  Am Lavenstein 3
  52064 Aachen
  GERMANY

  Web:    www.systemantics.net
  Email:  hello@systemantics.net

  Permission granted to use the files associated with this
  website only on your webserver.

  Changes to these files are PROHIBITED due to license restrictions.
*/



define('HEADLINE_PATH', 'headlines/');
define('HEADLINE_SUFFIX', '.gif');



function headlineBasename($id) {
	return sprintf('%06d', $id);
}

function headlineFilename($id, $headline) {
	return HEADLINE_PATH.'headline'.headlineBasename($id).'-'.preg_replace('/[^a-z0-9]/', '', strtolower($headline)).HEADLINE_SUFFIX;
}

?>