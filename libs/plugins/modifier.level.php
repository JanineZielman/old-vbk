<?php

/*
  String indention
  Copyright (C) 2008 by Systemantics, Bureau for Informatics

  Lutz Issler
  Mauerstr. 10-12
  47877 Willich
  GERMANY

  Web:    www.systemantics.net
  Email:  mail@systemantics.net

  This file is free to copy and use. No copyright notice
  must be maintained.
*/



function smarty_modifier_level($string,$level) {
	return $string
		? trim(str_repeat("\t", $level).str_replace("\n", "\n".str_repeat("\t", $level), $string))
		: '';
}

?>