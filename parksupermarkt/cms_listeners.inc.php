<?php

/*
  Park Supermarkt
  Copyright (C) 2012 by Systemantics, Bureau for Informatics

  Systemantics GmbH
  Alte Poststr. 38
  47877 Willich
  GERMANY

  Web:    www.systemantics.net
  Email:  hello@systemantics.net

  Permission granted to use the files associated with this
  website only on your webserver.

  Changes to these files are PROHIBITED due to license restrictions.
*/



define('PATH', '../images/');
define('CONVERT', substr($_SERVER['SERVER_NAME'], -6)=='.local' ? '/opt/local/bin/convert' : 'convert');



function resizeImage($fnSrc, $fnDst, $width, $height) {
	$cmd = CONVERT." $fnSrc -resize x".($height*2)." -resize '".($width*2)."x<' -resize 50% -gravity center -crop $width"."x$height+0+0 +repage $fnDst";
	exec($cmd);
}

function cmsAtomSaved($atom) {
}

function cmsAtomDelete($atom) {
}

?>