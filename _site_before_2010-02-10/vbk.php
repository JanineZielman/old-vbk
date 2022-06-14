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



require_once('content/config.inc.php');
require_once('content/database.inc.php');
require_once('vbk-core.inc.php');



// Calculate the article's path to the root
function getArticlePath($id, $parent, $db) {
	$article->path = array($article->id);
	$path = array();
	while ($parent!=0) {
		$path[] = $parent;
		$query = "SELECT parent FROM articles WHERE id=$parent";
		$db->query($query);
		$db->next_row();
		$parent = $db->row->parent;
	}
	return $path;
}



// Format a headline (callback)
function formatHeadline($matches) {
	$headline = trim($matches[1]);
	// Create headline filename
	$fn = headlineFilename($GLOBALS['id'], $headline);
	// Return headline image code
	$size = @GetImageSize($fn);
	return '<hr /><h1><img src="/'.$fn.'" '.$size[3].' alt="'.str_replace(array('\\', '  '), ' ', $headline).'" /></h1>'."\n";
}



function formatArticleText($id, $text) {
	define(DOUBLESLASH, '__DOUBLESLASH__');
	define(DOUBLEQUOTE, '__DOUBLEQUOTE__');

	// Format internal links with link text
	$text = preg_replace(
		array(
			'/\\[\\[(.*)\\|(.*)\\]\\]/U',
		),
		array(
			'<a href='.DOUBLEQUOTE.'\\1.html'.DOUBLEQUOTE.'>\\2</a>',
		),
		$text
	);
	// Format internal links without link text
	$text = preg_replace_callback(
		'/\\[\\[(.*)\\]\\]/U',
		create_function(
			'$matches',
			'$GLOBALS["db"]->query("SELECT title_".$GLOBALS["lang"]." AS title FROM articles WHERE id=".$matches[1]);$GLOBALS["db"]->next_row();return "<a href=".DOUBLEQUOTE.$matches[1].".html".DOUBLEQUOTE.">".str_replace("\\\\", " ", htmlentities(stripslashes($GLOBALS["db"]->row->title)))."</a>";'
		),
		$text
	);
	// Format links
	$text = preg_replace_callback(
		array(
			'/\\[(.*)\\|(.*)\\]/U',
			'/\\[(.*)\\]/U',
		),
		create_function(
			// Make links and escape all //
			'$matches',
			'if(count($matches)==3){$label=$matches[2];}else{$label=$matches[1];}return "<a href=".DOUBLEQUOTE.str_replace("//",DOUBLESLASH,$matches[1]).DOUBLEQUOTE." target=\"_blank\">".str_replace("//",DOUBLESLASH,$label)."</a>";'
		),
		trim(stripslashes($text))
	);
	// Format article text
	$text = preg_replace(
		array(
			'/\\/\\/(.*)\\/\\//Us',
		),
		array(
			'<span class="bijschrift">\\1</span>',
		),
		str_replace(
			array(
				'---',
			),
			array(
				'<hr />',
			),
			$text
		)
	);
	// Format headlines
	$text = preg_replace_callback(
		'/==\s*(.*)\s*==/',
		'formatHeadline',
		$text
	);
	// Re-replace escapings
	$text = str_replace(
		array(
			DOUBLESLASH,
			DOUBLEQUOTE,
		),
		array(
			'//',
			'"',
		),
		$text
	);
	// Add a heading ruler
	if (substr($text, 0, 6)!='<hr />') {
		$text = "<hr />$text";
	}
	// Format paragraphs
	$lines = preg_split('/^\s*$/m', $text, -1, PREG_SPLIT_NO_EMPTY);
	$text = '';
	foreach ($lines as $line) {
		$line = trim($line);
		// Check the beginning
		if (substr($line, 0, 2)=='<h'
			|| substr($line, 0, 6)=='<table'
			|| substr($line, 0, 3)=='<ul') {
			// Headline or table
			$text .= $line;
		} else {
			// Paragraph
			$text .= '<p>'.nl2br($line).'</p>';
		}
		$text .= "\n";
	}
	return $text;
}



// Determine language
$otherLang = false;
while (!$otherLang) {
	switch ($_REQUEST['lang']) {
		case LANG_NEDERLANDS:
			$lang = LANG_NEDERLANDS;
			$otherLang = LANG_ENGLISH;
			break;
		case LANG_ENGLISH:
			$lang = LANG_ENGLISH;
			$otherLang = LANG_NEDERLANDS;
			break;
		default:
			$_REQUEST['lang'] = LANG_DEFAULT;
	}
}



// Retrieve article from database
if ($_REQUEST['id']=='disclaimer') {
	$article = (object)null;
	$article->path = array();
	$article->level = 0;
	$article->text_nl = 'Disclaimer //De informatie verzonden met dit E-mail bericht is uitsluitend bestemd voor geadresseerde. Gebruik van deze informatie door andere dan de geadresseerde is onrechtmatig. De afzender is niet aansprakelijk voor wijziging, bewerking, doorzending en/of verstrekking aan derden van de hierbij elektronisch toegezonden data.//';
	$article->text_en = 'Disclaimer //The information transmitted is intended only for the person or entity to whom or which it is addressed. Unauthorized use, disclosure or copying is strictly prohibited. The sender accepts no liability for the improper transmission of this communication or for any delay in its receipt.//';
} else if (isset($_REQUEST['id'])) {
	$found = false;
	$query = 'SELECT * FROM articles WHERE id='.$_REQUEST['id'];
	$db->query($query);
	if (!$db->next_row()) {
		// No article with this id is in the database
		// Get the first article from the database instead
		$query = 'SELECT id FROM articles WHERE parent=0 ORDER BY ord LIMIT 1';
		$db->query($query);
		if (!$db->next_row()) {
			// There are no articles in the database
			// Print error message (styled as a warning)
			print 'The system is undergoing maintenance at the moment. Please try again later.';
			exit;
		}
		// Redirect to the new article's id
		header('Refresh: 0;URL='.articleURL($db->row->id, false, false));
		exit;
	}
	if ($db->row->parent==0) {
		// First-level article -> get the first subarticle
		$query = 'SELECT id FROM articles WHERE parent='.$db->row->id.' ORDER BY ord LIMIT 1';
		$db->query($query);
		// Get this article.
		if ($db->next_row()) {
			// Redirect to the new article's id
			header('Refresh: 0;URL='.articleURL($db->row->id, false, false));
			exit;
		}
	}
	// Store article
	$article = $db->row;
	$article->path = getArticlePath($article->id, $article->parent, $db);
	$article->level = count($article->path);
} else {
	// No ID specified -> display recent page
	$article = false;
}



// Set meta information
$meta = array();
$meta['author'] = 'Jago van Bergen, Evert Kolpa';
$meta['publisher'] = $meta['author'];
$meta['copyright'] = $meta['publisher'];
$meta['identifier'] = 'http://www.vanbergenkolpa.nl/';
$meta['contributors'] = $meta['author'].', Daniel Gross, Joris Maltha, Niels van der Sluijs, Lutz Issler';
$meta['desc'] = 'van Bergen Kolpa Architecten richten zich op architectuur, stedenbouw en landschap. Centraal in de werkwijze staat het vormen van een balans tussen programma, landschap en gebouwen, ecologie, duurzaamheid, water, Jago van Bergen, Evert Kolpa';
$meta['keywords'] = 'vanbergenkolpa, www.vanbergenkolpa.nl, vbk, duurzaamheid, ecologie, water architect, balans, ecology, sustainability, Lofvers van Bergen Kolpa, lvbk, colpa, kopla, jaco van de berg, Architectenbureau, Architectuur Nederland, Kaap de Goede Hoek, Co Housing, scholen campus, Campus Hoogvliet, Mullerpier, Zilte ProefTuin, Archiprix, Prix de Rome, Charlotte Köhlerprijs, Innovation Award, International Architecture Biennale, architect the Netherlands, architecture research, recreatie, Agenda Rotterdam, Stroom, Foodprint, air Foundation';



if (!$article) {
	// Prevent caching of starting page
	header('Cache-Control: no-cache');
	header('Pragma: no-cache');
	header('Expires: -1');
}



print '<?'; ?>xml version="1.0" encoding="iso-8859-1"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/2000/REC-xhtml1-20000126/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>van Bergen Kolpa Architecten, Dutch architect based in Rotterdam</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta name="Description" content="<?php print $meta['desc'];?>" />
	<meta name="Keywords" content="<?php print $meta['keywords'];?>" />
	<meta name="Author" content="<?php print $meta['author'];?>" />
	<meta name="Copyright" content="<?php print $meta['copyright'];?>" />
	<meta name="Publisher" content="<?php print $meta['publisher'];?>" />
	<meta name="DC.TITLE" content="van Bergen Kolpa Architecten, Dutch architect based in Rotterdam" />
	<meta name="DC.CREATOR" content="<?php print $meta['author'];?>" />
	<meta name="DC.SUBJECT" content="<?php print $meta['keywords'];?>" />
	<meta name="DC.DESCRIPTION" content="<?php print $meta['desc'];?>" />
	<meta name="DC.PUBLISHER" content="<?php print $meta['publisher'];?>" />
	<meta name="DC.CONTRIBUTORS" content="<?php print $meta['contributors'];?>" />
	<meta name="DC.DATE" content="<?php print strftime('%Y-%m-%d'); ?>" />
	<meta name="DC.TYPE" content="InteractiveResource" />
	<meta name="DC.FORMAT" content="text/html" />
	<meta name="DC.IDENTIFIER" content="<?php print $meta['identifier']; ?>" />
	<meta name="DC.LANGUAGE" content="<?php print $lang; ?>" />
	<meta name="DC.RIGHTS" content="<?php print $meta['copyright'];?>" />
	<link rel="stylesheet" href="/vbk.css" type="text/css" />
	<script src="http://www.google-analytics.com/urchin.js" type="text/javascript"></script>
	<!--
		Design by Daniel Gross and Joris Maltha, Catalogtree, Arnehm/The Netherlands (http://www.catalogtree.nl/)
		Programming by Lutz Issler, Systemantics, Aachen/Germany (http://www.systemantics.net/)
	-->
</head>

<body>
<div><span class="zwart"><a href="/<?php print $GLOBALS['lang']; ?>/"><img src="/elements/VBK-logo3b.gif" border="0" alt="van Bergen Kolpa Architecten" /></a></span>
  <div id="nav">
    <div id="navkop1"><a href="<?php print $article ? articleURL($id, $otherLang) : '/'.$otherLang.'/'; ?>"><?php print $LANG_NAME[$otherLang]; ?></a><?php

	// Print the first-level navigation
	$query = "SELECT id,title_$lang AS title FROM articles WHERE parent=0 ORDER BY ord";
	$db->query($query);
	while ($db->next_row()) {
		print ' / <a href="'.articleURL($db->row->id).'"'.($article->path[0]==$db->row->id ? ' class="hier"' : '').'>'.htmlentities(stripslashes($db->row->title)).'</a>';
	}

?></div>
<div id="navkop2"><?php

	// Print the second-level navigation
	$query = "SELECT id,title_$lang AS title FROM articles WHERE parent=".$article->path[0]." ORDER BY ord";
	$db->query($query);
	if ($db->num_rows()>1) {
		$first = true;
		while ($db->next_row()) {
			if ($first) {
				$first = false;
			} else {
				print ' / ';
			}
			print '<a href="'.articleURL($db->row->id).'"'.($db->row->id==$article->id ? ' class="hier"' : '').'>'.htmlentities(stripslashes($db->row->title)).'</a>';
		}
	}

?></div>
    </div>

</div>
<br />
<div id="content">
<div id="beeld">
<hr />
<?

	// Print images
if ($article) {
	$first = true;
	for ($i=1; $i<=NUM_PICTURES; $i++) {
		$fn = IMAGE_PATH.'article'.sprintf('%06d-%02d', $id, $i).IMAGE_SUFFIX;
		if (file_exists($fn)) {
			// Print image
			$size = GetImageSize($fn);
			if ($first) {
				$first = false;
			} else {
?><hr />
<br />
<?php
			}
?><img src="<?php print '/'.$fn; ?>" alt="" />
<?php
		}
	}
}

?></div>

<div id="text">
<?php

	// Print article text
	if ($article) {
		$text = trim($article->{"text_$lang"});
		if ($text=='') {
			$text = $NO_TEXT_AVAILABLE[$lang];
		}
		print formatArticleText($article->id, $text);
	}

?></div>
</div>
<?php

	// Display recent image, if no article was displayed
	if (!$article) {
		// Get the message (if any)
		$query = "SELECT message_$lang AS message,color,article FROM messages LIMIT 0,1";
		$db->query($query);
		$message = $db->next_row() && is_file("headlines/message_$lang.gif")
			? "<div class=\"message\"><img src=\"/headlines/message_$lang.gif\" alt=\"".htmlspecialchars(str_replace(array("\n", "\r"), '', $db->row->message))."\" />".($db->row->article==0 ? '' : '<br /><br /><br /><br /><a style="background-color:#'.$db->row->color.';" href="/'.$lang.'/'.$db->row->article.'.html">'.($lang=='en' ? 'Read more' : 'Lees meer')." &gt;&gt;&gt;</a>")."</div>"
			: '';

		// Collect the possible recent images
		$recent = array();
		for ($i=1; $i<=NUM_RECENT_IMAGES; $i++) {
			$fn = IMAGE_PATH.sprintf(RECENT_IMAGE, $i);
			if (file_exists($fn)) {
				$recent[] = $fn;
			}
		}
		$numRecent = rand(0, count($recent)-1);
?><div id="recent"><?php echo $message; ?><img src="<?php print '/'.$recent[$numRecent]; ?>" width="100%" height="80%" alt="" /></div>
<?php
	}

?><script type="text/javascript">
	_uacct = "UA-3748362-1";
	urchinTracker();
</script>
</body>
</html>
