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



function smarty_modifier_shortenlinks($string) {
	// match protocol://address/path/file.extension?some=variable&another=asf%
	$string = preg_replace("/\b(([a-zA-Z]+:\/\/)([a-z][a-z0-9_\..-]*[a-z]{2,6})([a-zA-Z0-9\/*-?&%]*))\b/i", "<a href=\"$1\">$3</a>", $string, -1, $c);

	if ($c==0) {
		// match www.something.domain/path/file.extension?some=variable&another=asf%
		$string = preg_replace("/\b(www\.([a-z][a-z0-9_\..-]*[a-z]{2,6})([a-zA-Z0-9\/*-?&%]*))\b/i", "<a href=\"http://$1\">$2</a>", $string);
	}

	return $string;
}

?>