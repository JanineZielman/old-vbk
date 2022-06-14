<?php

/*
  van Bergen Kolpa
  Copyright (C) 2006-2010 by Systemantics, Bureau for Informatics

  Systemantics GmbH
  Am Lavenstein 3
  52064 Aachen
  GERMANY

  Web:    www.systemantics.net
  Email:  hello@systemantics.net

  Permission granted to use the files associated with this
  website only on your webserver.

  Changes to these files are PROHIBITED due to license restrictions.
*/



require_once 'element_main.inc.php';
require_once 'func.headlines.inc.php';



// Format a headline (callback)
function formatHeadline($matches) {
	$headline = trim($matches[1]);
	// Create headline filename
	$fn = headlineFilename($GLOBALS['param'], $headline);
	// Return headline image code
	$size = @GetImageSize($fn);
	return '<hr /><h1><img src="/'.$fn.'" '.$size[3].' alt="'.str_replace(array('\\', '  '), ' ', $headline).'" /></h1>'."\n";
}



function formatArticleText($text) {
	define('DOUBLESLASH', '__DOUBLESLASH__');
	define('DOUBLEQUOTE', '__DOUBLEQUOTE__');

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
		function ($matches) {
			$article = $GLOBALS["portal"]->getAtom($matches[1]);
			if (!$article) {
				return false;
			}
			return "<a href=".DOUBLEQUOTE.$article->_id.".html".DOUBLEQUOTE.">".str_replace("\\\\", " ", htmlentities(stripslashes($article->title), 0, 'iso-8859-1'))."</a>";
		},
		$text
	);
	// Format links
	$text = preg_replace_callback(
		array(
			'/\\[(.*)\\|(.*)\\]/U',
			'/\\[(.*)\\]/U',
		),
		// Make links and escape all //
		function ($matches) {
			if (count($matches) == 3){
				$label = $matches[2];
			} else {
				$label = $matches[1];
			}
			return "<a href=".DOUBLEQUOTE.str_replace("//",DOUBLESLASH,htmlspecialchars($matches[1], 0, 'iso-8859-1')).DOUBLEQUOTE." class=\"external\">".str_replace("//",DOUBLESLASH,$label)."</a>";
		},
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
	/*
	$text = preg_replace_callback(
		'/==\s*(.*)\s*==/',
		'formatHeadline',
		$text
	);
	*/
	$text = preg_replace(
		'/==\s*(.*)\s*==/',
		'<h1>$1</h1>',
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



$article = $portal->getAtom($param);

if ($article->_type=='section') {
	// Redirect to the first article in this section
	$article = @reset($portal->getAtoms($article->_id, 'articles', 0, 1));
	if ($article) {
		require_once 'libs/plugins/modifier.sluggize.php';
		header('Location: http://'.$_SERVER['HTTP_HOST']."/$lang/$article->_id".'_'.smarty_modifier_sluggize($article->title).".html");
		exit;
	}
	echo 'There is no such article.';
	exit;
}

$section = $portal->getAtom($article->_owner);

// Set page title and description
$portal->setPageTitle(($article->title!=$section->title ? $article->title.' / ' : '').$section->title.' / '.$portal->getPageTitle());

// Format article images and text
$article->images = array();
$thisRow = array();
$prevImagesperrow = 0;
foreach ($portal->getAtoms($article->_id, 'images') as $image) {
	// Get the image or video
	$ok = preg_match('/^https?:\/\/www\.youtube\.com\/watch\?v=([a-zA-Z0-9\-]+)$/', $image->youtubeurl, $matches);
	if ($ok) {
		$image->youtubeid = $matches[1];
		$image->size = array(400, 265);
	} else {
		$image->size = @getimagesize($image->image);
	}

	if ($image->imagesperrow != $prevImagesperrow) {
		// Add the current row and open a new row
		if (count($thisRow) > 0) {
			$article->images[] = $thisRow;
		}
		$thisRow = array();
		$prevImagesperrow = $image->imagesperrow;
	}

	// Add image to current row
	$thisRow[] = $image;

	// If the row is full, add it
	if (count($thisRow) == $image->imagesperrow) {
		$article->images[] = $thisRow;
		$thisRow = array();
	}
}

$article->text = formatArticleText($article->text);



$smarty->assign(array(
	'article' => $article,
	'section' => $section,
	'articles' => $portal->getAtoms($section->_id, 'articles'),
));

?>
