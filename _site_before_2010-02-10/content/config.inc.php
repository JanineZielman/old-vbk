<?php

/*
  Content Management System
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



// Logo
define('LOGO', '<img src="../elements/VBK-logo3b.gif" border="0">&nbsp;');
define('LOGO_PLAIN', 'van Bergen Kolpa');



// Database configuration
define('DB_USERNAME', 'vanbergenko');
define('DB_PASSWORD', 'uhesube');
define('DB_HOSTNAME', 'localhost');
define('DB_DATABASE', 'vanbergenko');



// Content management access
define('CMS_USERNAME', 'jagob');
define('CMS_PASSWORD', 'j4p8e42X');



// Button types
define("BUTTON_BACK",'back');
define("BUTTON_NEW",'new');
define("BUTTON_OK",'ok');
define("BUTTON_SAVE", 'save');
define("BUTTON_CANCEL",'cancel');
define("BUTTON_YES",'yes');
define("BUTTON_NO",'no');



// Languages
define('LANG_NEDERLANDS', 'nl');
define('LANG_ENGLISH', 'en');
define('LANG_DEFAULT', LANG_ENGLISH);

$LANG = array(
	LANG_NEDERLANDS => 'Nederlands',
	LANG_ENGLISH => 'English',
);
$LANG_NAME = array(
	LANG_NEDERLANDS => 'NL',
	LANG_ENGLISH => 'ENG',
);

$NO_TEXT_AVAILABLE[LANG_NEDERLANDS] = 'Sorry, geen text beschikbaar!';
$NO_TEXT_AVAILABLE[LANG_ENGLISH] = 'Sorry, no text available!';



// Maximum article depth
define('MAXIMUM_ARTICLE_LEVEL', 1);



// Number of pictures per article
define('NUM_PICTURES', 10);
define('NUM_RECENT_IMAGES', 10);



// Images
define('PATH_PREFIX', '../');
define('IMAGE_PATH', 'images/');
define('IMAGE_SUFFIX', '.jpg');
define('IMAGE_WIDTH', 400);
define('RECENT_IMAGE', 'recent%02d.jpg');



// Automatic headline generation
define('HEADLINE_PATH', 'headlines/');
define('HEADLINE_SUFFIX', '.gif');
define('HEADLINE_FONT', './VBK001.ttf');
define('HEADLINE_FONT_SIZE', 18);
define('HEADLINE_FIRST_BASELINE', 17);
define('HEADLINE_VERSAL_HEIGHT', 22);
define('HEADLINE_LINE_HEIGHT', 30);



// ID for new articles
define('ID_NEW', -1);



?>
