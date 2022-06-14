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



require_once 'content/portal.inc.php';
require_once 'libs/plugins/modifier.sluggize.php';



$portal = new portal();

header('text/xml; charset=iso-8859-15');

print '<?';

?>xml version="1.0" encoding="ISO-8859-15"?>
<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">
	<url>
		<loc>http://www.vanbergenkolpa.nl/</loc>
		<changefreq>daily</changefreq>
	</url>
<?php



foreach (array('nl', 'en') as $lang) {
	$portal->setLanguage($lang);
	foreach ($portal->getAtoms(SITE_ID, 'sections') as $section) {
		foreach ($portal->getAtoms($section->_id, 'articles') as $article) {

?>	<url>
		<loc>http://www.vanbergenkolpa.nl/<?php echo $lang; ?>/<?php echo $article->_id; ?>_<?php echo smarty_modifier_sluggize($article->title); ?>.html</loc>
		<changefreq>weekly</changefreq>
	</url>
<?php

		}
	}
}

?>
</urlset>