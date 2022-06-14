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



error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);



require_once 'content/portal.inc.php';
//require_once 'ensure_www.inc.php';



$ACTIONS = array(
	'home' => array(
		'show',
	),
	'answers' => array(
		'byperson',
		'byquestion',
	),
);



ini_set('zlib.output_compression', 1);
ini_set('session.use_only_cookies', 1);

$portal = new portal();

session_set_cookie_params(0);
session_name(md5($portal->getSitename()));
session_start();

$portal->setLanguage('nl');
$lang = $portal->getLanguage();
setlocale(LC_TIME, $lang.'_'.strtoupper($lang));
date_default_timezone_set('Europe/Amsterdam');

$documentroot = '/parksupermarkt/';
$homeUrl = $documentroot.'';

// Try to get content by id
$realm = $_GET['realm'];
$action = $_GET['action'];
$param = $_GET['param'];
$id = $_GET['id'];
if (!isset($ACTIONS[$realm])) {
	// No valid realm specified
	// Use first realm by default
	$realm = reset(array_keys($ACTIONS));
}
if (!in_array($action, $ACTIONS[$realm])) {
	// No valid action specified
	// Shift URL parameters one step back
	$index = $id;
	$id = $param;
	$param = $action;
	// Use first action by default
	$action = reset($ACTIONS[$realm]);
}

// Set page title
$portal->setPageTitle($portal->getSitename());

// Normal actions

header('Content-Type: text/html; charset=utf-8');

require_once 'libs/Smarty.class.php';
$smarty = new Smarty();

$smarty->debugging = false;
$smarty->caching = false;
$smarty->cache_lifetime = 120;

$smarty->setTemplateDir('templates');
$smarty->setCompileDir('cache/templates');
$smarty->setCacheDir('cache');
$smarty->setConfigDir('locales');

$smarty->configLoad("$lang.ini");

$template = $realm.'_'.$action;
$smarty->assign('content', "$template.tpl");

$filename = "controllers/$template.inc.php";
if (is_readable($filename)) {
	include $filename;
} else {
	echo "Cannot open controller '$filename'.";
	exit;
}

// Set URL info
$smarty->assign('lang', $portal->getLanguage());
$smarty->assign('realm', $realm);
$smarty->assign('action', $action);
$smarty->assign('param', $param);
$smarty->assign('id', $id);

$smarty->assign('meta', $portal->getMetadata());
$smarty->assign('documentroot', $documentroot);
$smarty->assign('homeUrl', $homeUrl);
$smarty->display("$template.tpl");

?>