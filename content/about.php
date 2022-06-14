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
$config = new config();
$page = new page($portal->getSitename(), $lang, $config);

$page->page_preface(ucwords($lang->l('cms')));
$page->page_head('About this software');

?><p>This is the <?php print CMS_LOGO; ?> Content Management System,
installed on the website of <?php print $portal->getSitename(); ?>.
You are running version <?php print CMS_VERSION; ?>.</p>
<h2>Legal note</h2>
<p>This is a commercial software product which is protected by copyright.
You have purchased a license which allows you to use the software
for maintaining the contents of the website of <?php print $portal->getSitename(); ?>.
The software itself does <em>not</em> »belong« to you in any sense of the word.
This means that you are not allowed to modify the software, to distribute it, or to
use it for the maintenance of another website other than the one of <?php print $portal->getSitename(); ?>.</p>
<p>The Copyright &copy for this software belongs to</p>
<p class="indented">Systemantics,<br>
Bureau for Informatics<br>
Mauerstr. 10–12<br>
52064 Aachen<br>
GERMANY</p>
<p class="indented">Web page: <a href="http://www.systemantics.net/" target="_blank" title="<?php print $lang->l('showpyrrhon'); ?>">http://www.systemantics.net/</a></p>
<p class="indented">Email: <a href="mailto:mail@systemantics.net" title="Send an email to Systemantics">mail@systemantics.net</a></p>
<p>This software makes use of the TinyMCE editor, which is open source software published  under the <a href="tiny_mce/license.txt" target="_blank" title="View the LGPL">GNU Library General Public License</a> (LGPL).
TinyMCE is copyrighted by <a href="http://www.moxiecode.com/" target="_blank" title="Website of Moxiecode Systems">Moxiecode Systems AB</a>.
It is available for download from <a href="http://tinymce.moxiecode.com/" target="_blank" title="Website of TinyMCE">http://tinymce.moxiecode.com/</a>.</p>
<p>&nbsp;</p>
</body>
</html>