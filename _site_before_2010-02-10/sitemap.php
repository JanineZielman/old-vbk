<?php

/*
  van Bergen Kolpa

  Copyright (C) 2008 by Systemantics, Bureau for Informatics

  Systemantics,
  Bureau for Informatics
  Mauerstr. 10-12
  52064 Aachen
  GERMANY

  Web:    www.systemantics.net
  Email:  mail@systemantics.net

  Permission granted to use this file only on the server of van Bergen Kolpa.
*/



require_once('content/config.inc.php');
require_once('content/database.inc.php');
require_once('vbk-core.inc.php');






header('text/xml; charset=iso-8859-15');

print '<?';

?>xml version="1.0" encoding="ISO-8859-15"?>
<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">
	<url>
		<loc>http://www.vanbergenkolpa.nl/</loc>
		<changefreq>daily</changefreq>
	</url>
<?php

$query = 'SELECT id FROM `articles` WHERE text_nl!=""';
$db->query($query);
while ($db->next_row()) {
	foreach (array('nl', 'en') as $lang) {

?>	<url>
		<loc>http://www.vanbergenkolpa.nl<?php print articleURL($db->row->id, $lang); ?></loc>
		<changefreq>weekly</changefreq>
	</url>
<?php

	}
}

?>
</urlset>