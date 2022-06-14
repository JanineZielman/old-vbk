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



require_once 'element_main.inc.php';



$person = $portal->getAtom($param, 'person');
if (!$person) {
	exit;
}

$smarty->assign('person', $person);

$portal->setPageTitle($person->name.' - '.$portal->getPageTitle());

$filter = "`person`='$person->_id'";
require_once 'element_answers.inc.php';

$template = 'answers_list';

?>