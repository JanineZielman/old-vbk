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



$question = $portal->getAtom($param, 'question');
if (!$question) {
	exit;
}

$smarty->assign('question', $question);

$portal->setPageTitle($question->shortquestion.' - '.$portal->getPageTitle());

$filter = "`question`='$question->_id'";
require_once 'element_answers.inc.php';

$template = 'answers_list';

?>