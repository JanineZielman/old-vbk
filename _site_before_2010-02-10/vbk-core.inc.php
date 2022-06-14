<?php

/*
  van Bergen Kolpa

  Copyright (C) 2006 Pyrrhon Software GbR

  Lutz und Arnd Ißler GbR
  Paul-Klee-Str. 54
  47877 Willich
  GERMANY

  Web:    www.pyrrhon.com
  Email:  info@pyrrhon.com

  Permission granted to use this file only on the server of van Bergen Kolpa.
*/



function headlineBasename($id) {
	return sprintf('%06d', $id);
}



function headlineFilename($id, $headline) {
	return HEADLINE_PATH.'headline'.headlineBasename($id).'-'.preg_replace('/[^a-z0-9]/', '', strtolower($headline)).HEADLINE_SUFFIX;
}



function articleURL($id, $l = false, $encoded = true) {
	return '/'.($l ? $l : $GLOBALS['lang']).'/'.$id.'.html';
}



?>