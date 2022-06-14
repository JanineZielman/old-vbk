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



$colors = array(
	'e42423',
	'334a98',
	'58c3e5',
	'59b584',
	'f6ea5a',
);

$news = array();
foreach ($portal->getAtoms(SITE_ID, 'news') as $aNews) {
	$aNews->color = $colors[rand(0, count($colors)-1)];
	$news[] = $aNews;
}

$backgroundimage = @reset($portal->getAtoms(SITE_ID, 'backgroundimages', rand(0, $portal->getAtomCount(SITE_ID, 'backgroundimages')-1), 1));

$smarty->assign(array(
	'persons' => $portal->getAtoms(SITE_ID, 'persons'),
	'questions' => $portal->getAtoms(SITE_ID, 'questions'),
	'news' => $news,
	'backgroundimage' => $backgroundimage,
));

?>
