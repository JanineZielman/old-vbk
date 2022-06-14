<?php

/*
  Content Management System

  Copyright (C) 2006–2008 Systemantics

  Systemantics,
  Bureau for Informatics
  Mauerstr. 10-12
  52064 Aachen
  GERMANY

  Web:    www.systemantics.net
  Email:  mail@systemantics.net

  This file is NOT free software. Modification or usage of this file without
  buying an appropriate license from Systemantics breaks international
  copyright laws.
*/



require_once('config.inc.php');
require_once('content.inc.php');
require_once('lang.inc.php');



header('Content-Type: text/javascript');

// Create the config access object
$configFile = PATH_PREFIX.PATH_CONFIG.PATH_CONFIG_FILE;
$config = new config($configFile);
$lang = new lang();

// Create the content access object
$siteFile = PATH_PREFIX.PATH_CONFIG.$config->value(CONFIG_CMS_SITE_FILE, PATH_SITE_FILE);
if (is_readable($siteFile)) {
	$content = new content(
		$siteFile,
		$config->value(CONFIG_DATABASE_HOSTNAME),
		$config->value(CONFIG_DATABASE_DATABASE),
		$config->value(CONFIG_DATABASE_USERNAME),
		$config->value(CONFIG_DATABASE_PASSWORD),
		$config->value(CONFIG_CMS_TABLEPREFIX, false)
	);
} else {
	exit;
}

$linkableTypes = $config->value(CONFIG_CMS_LINKABLE_ATOM_TYPES, false);
if (!$linkableTypes) {
	$lists = $content->getLists(SITE);
} else {
	$lists = array();
	foreach(explode(',', $linkableTypes) as $linkableType) {
		$lists[] = $content->getListInfo(SITE, $linkableType);
	}
}
print "var tinyMCELinkList = new Array(\n";
$i = 0;
foreach ($lists as $listInfo) {
	foreach ($content->getAtoms(SITE_ID, $listInfo) as $atom) {
		print ($i>0 ? ",\n" : '')."\t".'["'.($atom->_label=='' ? '('.$lang->l('emptylabel').')' : $atom->_label).'", "[['.$atom->_id."]]\"]";
		$i++;
	}
}
print "\n);\n";



?>