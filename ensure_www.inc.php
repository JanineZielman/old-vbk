<?php

/*
  Ensure WWW
  Copyright (C) 2008 by Systemantics, Bureau for Informatics

  Lutz Issler
  Mauerstr. 10-12
  52064 Aachen
  GERMANY

  Web:    www.systemantics.net
  Email:  mail@systemantics.net

  Permission granted to use the files associated with this
  website only on your webserver.
*/


if (strpos($_SERVER['HTTP_HOST'], '.')!==false && !preg_match('/^www\.[a-z-]+\.[a-z]{2,6}/', $_SERVER['HTTP_HOST'])) {
	header('Location: http://www.'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	exit;
}

?>