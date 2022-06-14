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



require_once 'element_main.inc.php';
require_once 'libs/plugins/modifier.sluggize.php';



$article = $portal->getAtom($param);
if ($article) {
	header('HTTP/1.1 301 Moved Permanently'); 
	header('Location: http://'.$_SERVER['HTTP_HOST']."/$lang/$article->_id".'_'.smarty_modifier_sluggize($article->title).".html");
	exit;
}
echo 'There is no such article.';
exit;

?>