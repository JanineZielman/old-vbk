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



$smarty->assign(array(
	'sections' => $portal->getAtoms(SITE_ID, 'sections'),
));

?>