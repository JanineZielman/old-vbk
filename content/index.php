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
require_once('lang.inc.php');
require_once('page.inc.php');
require_once('portal.inc.php');



$portal = new portal(PATH_PREFIX);
$lang = new lang();
$config = new config(isset($configFile) ? $configFile : false);
$page = new page($portal->getSitename(), $lang, $config);

$page->page_preface($lang->l('cms'));
$portal->printMetadata($lang->lang, '');
$logo = $config->value(CONFIG_CMS_LOGO, false);
if ($logo) {
	$start = '<img src="'.PATH_PREFIX.PATH_CONFIG.$logo.'" alt="'.$portal->getSitename().'">';
} else {
	$start = strtoupper($portal->getSitename());
}

?>
	<div id="titleHorizon">
		<div id="titleContent">
			<span class="titleHeadline"><a href="content.php" onclick="window.open('content.php','cms','directories=0,location=0,menubar=0,resizable=1,scrollbars=1,status=0,titlebar=1,toolbar=0').focus();return false;" title="<?php print ucfirst($lang->l('start')).' '.$lang->l('cms'); ?>"><?php print $start; ?><img src="elements/right.gif"></a></span>
		</div>
	</div>
	<div id="titleFooter">
		<?php print $page->getFooter(); ?>
	</div>
</body>
</html>