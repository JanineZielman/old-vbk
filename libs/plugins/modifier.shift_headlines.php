<?php

/*
  Shift headline levels
  Copyright (C) 2008 by Systemantics, Bureau for Informatics

  Systemantics GmbH
  Alte Poststrasse 38
  47877 Willich
  GERMANY

  Web:    www.systemantics.net
  Email:  hello@systemantics.net

  This file is free to copy and use. No copyright notice
  must be maintained.
*/



function smarty_modifier_shift_headlines($s, $by = 1) {
	for ($i=6-$by; $i>=1; $i--) {
		$s = str_replace("<h$i>", '<h'.($i+$by).'>', $s);
		$s = str_replace("</h$i>", '</h'.($i+$by).'>', $s);
	}
	return $s;
}

?>