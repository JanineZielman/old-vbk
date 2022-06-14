<?php

/*
  String indention
  Copyright (C) 2008 by Systemantics, Bureau for Informatics

  Lutz Issler
  Mauerstr. 10-12
  52064 Aachen
  GERMANY

  Web:    www.systemantics.net
  Email:  mail@systemantics.net

  This file is free to copy and use. No copyright notice
  must be maintained.
*/



function smarty_modifier_sluggize($s) {
	return str_replace(array(' ', '-', '__'), '_', preg_replace('/[^a-z0-9 -]/', '', str_replace(array('‰', 'ˆ', '¸'), array('ae', 'oe', 'ue'), strtolower(trim($s)))));
}

?>