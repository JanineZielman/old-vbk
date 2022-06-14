<?php

/*
  Systemantics Downloader

  Copyright (C) 2014 by Systemantics, Bureau for Informatics

  Systemantics GmbH
  Bleichstr. 11
  41747 Viersen
  GERMANY

  Web:    www.systemantics.net
  Email:  hello@systemantics.net

  Permission granted to use the files associated with this
  website only on your webserver.

  Changes to these files are PROHIBITED due to license restrictions.
*/



$documentroot = '/';



// Get file
$fn = '../downloads/' . basename($_GET['file_name']);
if (!$fn || !file_exists($fn) || !is_file($fn)) {
	exit;
}

// Get MIME type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimetype = finfo_file($finfo, $fn);
finfo_close($finfo);

header('Content-Type: ' . $mimetype);
header('Content-Disposition: attachment; filename=' . pathinfo($fn, PATHINFO_BASENAME));
header('Content-Length: ' . filesize($fn));

readfile($fn);

exit;
