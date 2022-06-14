<?php

/*
  Half Full
  Copyright (C) 2010 by Systemantics, Bureau for Informatics

  Systemantics GmbH
  Am Lavenstein 3
  52064 Aachen
  GERMANY

  Web:    www.systemantics.net
  Email:  mail@systemantics.net

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
	'articles' => array(
		'show',
		'legacy',
	),
);



$portal = new portal();
$portal->setLanguage($_GET['lang']);
$lang = $portal->getLanguage();
setlocale(LC_TIME, $lang.'_'.strtoupper($lang));

$documentroot = '/';

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
$portal->setPageTitle('van Bergen Kolpa Architecten, Dutch architects based in Rotterdam');

// Normal actions

header('Content-Type: text/html; charset=utf-8');

require_once 'libs/Smarty.class.php';
$smarty = new Smarty();

$smarty->template_dir = 'templates';
$smarty->compile_dir = 'cache/templates';
$smarty->cache_dir = 'smarty/cache';
$smarty->config_dir = 'locales';

$smarty->configLoad("$lang.ini");

function trimemptytags($source, &$smarty) {
	return preg_replace(
		array(
			'|<h1(\s[^>]*)?>\s*</h1>|',
			'|<h2(\s[^>]*)?>\s*</h2>|',
			'|<h3(\s[^>]*)?>\s*</h3>|',
			'|<ul(\s[^>]*)?>\s*</ul>|',
			':<p(\s[^>]*)?>(&nbsp;|\s)*</p>:',
			':<p(\s[^>]*)?><!--.*--></p>:',
			'|<b(\s[^>]*)?>\s*</b>|',
			'|<i(\s[^>]*)?>\s*</i>|',
			'|<u(\s[^>]*)?>\s*</u>|',
			'|<!--\[if gte mso.*<!\[endif\]-->|sU',
		),
		'',
		$source
	);
}

$smarty->registerFilter('output', 'trimemptytags');

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
$smarty->display("$template.tpl");

?>