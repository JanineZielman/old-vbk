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



$answers = array();
foreach ($portal->getAtoms(SITE_ID, 'answers', false, false, $filter) as $aAnswer) {
	$aAnswer->person = $person ? $person : ($aAnwer->person==SITE_ID ? false : $portal->getAtom($aAnswer->person, 'person'));
	$aAnswer->question = $question ? $question : ($aAnwer->question==SITE_ID ? false : $portal->getAtom($aAnswer->question, 'question'));
	$answers[sprintf('%02d-%02d', $aAnswer->question->_order, $aAnswer->person->_order)] = $aAnswer;
}

ksort($answers);

$smarty->assign('answers', $answers);

?>