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



require_once 'controllers/func.headlines.inc.php';



define('PATH_PREFIX', '../');

define('HEADLINE_PATH', 'headlines/');
define('HEADLINE_SUFFIX', '.gif');
define('HEADLINE_FONT', '../elements/VBK001.ttf');
define('HEADLINE_FONT_SIZE', 18);
define('HEADLINE_FIRST_BASELINE', 17);
define('HEADLINE_VERSAL_HEIGHT', 22);
define('HEADLINE_LINE_HEIGHT', 30);



function cmsTextSaved($article) {
	foreach (array('en', 'nl') as $lang) {
		// Generate the headlines for this article and language
		preg_match_all('/==\s*(.+)\s*==/', $article->{"text_$lang"}, $heads);
		foreach ($heads[1] as $headline) {
			// Create headline filename
			$fn = PATH_PREFIX.headlineFilename($_REQUEST['id'], $headline);
			// Create headline image
			if (!file_exists($fn)) {
				// Split headline into lines
				$headlines = explode('\\\\', $headline);
				// Determine headline image dimensions
				$width = 0;
				$height = 0;
				$lineHeight = array();
				foreach ($headlines as $line) {
					$bbox = imagettfbbox(
						HEADLINE_FONT_SIZE,
						0,
						HEADLINE_FONT,
						trim($line)
					);
					$width = max($width, max($bbox[0],$bbox[2],$bbox[4],$bbox[6]) - min($bbox[0],$bbox[2],$bbox[4],$bbox[6]));
				}
				// Add some extra pixels on the right as workaround for
				// year numbers which were cut off on the right
				// [LI 2006-07-17]
				$width += 3;
				$height = HEADLINE_VERSAL_HEIGHT+(count($headlines)-1)*HEADLINE_LINE_HEIGHT;
				$img = imagecreatetruecolor($width, $height);
				imagefill($img, 0, 0, imagecolorallocate($img, 255, 255, 255));
				// Write text
				$baseline = HEADLINE_FIRST_BASELINE;
				foreach ($headlines as $line) {
					imagettftext(
						$img,
						HEADLINE_FONT_SIZE,
						0,
						0,
						$baseline,
						imagecolorallocate(
							$img,
							0,
							0,
							0
						),
						HEADLINE_FONT,
						trim($line)
					);
					$baseline += HEADLINE_LINE_HEIGHT;
				}
				// Save the image file
				imagegif($img, $fn);
				// Free resources
				imagedestroy($img);
			}
		}
	}
}

function cmsHomepageSaved($homepage) {
	define('MESSAGE_LINE_HEIGHT', HEADLINE_LINE_HEIGHT*2.5);

	// Create an image from the message
	$factor = 3;
	foreach (array('en', 'nl') as $lang) {
		$fn = "../headlines/message_$lang.gif";
		@unlink($fn);

		$message = strtoupper(trim($homepage->{"text_$lang"}));
		if (!$message) {
			continue;
		}
		$lines = explode("\n", $message);

		$bbox = imagettfbbox(
			HEADLINE_FONT_SIZE*$factor,
			0,
			HEADLINE_FONT,
			$message
		);
		$width = max($bbox[0],$bbox[2],$bbox[4],$bbox[6]) - min($bbox[0],$bbox[2],$bbox[4],$bbox[6])+3;
		$height = HEADLINE_FIRST_BASELINE*$factor+(count($lines)-1)*MESSAGE_LINE_HEIGHT;

		if ($height<0) {
			continue;
		}
		$img = imagecreatetruecolor($width, $height);
		$backcolor = imagecolorallocate($img, 255, 255, 255);
		imagecolortransparent($img, $backcolor);
		imagefill($img, 0, 0, $backcolor);
		// Write text
		$y = HEADLINE_FIRST_BASELINE*$factor;
		foreach ($lines as $line) {
			imagettftext(
				$img,
				HEADLINE_FONT_SIZE*$factor,
				0,
				0,
				$y,
				imagecolorallocate(
					$img,
					hexdec(substr($homepage->color, 0, 2)),
					hexdec(substr($homepage->color, 2, 2)),
					hexdec(substr($homepage->color, 4, 2))
				),
				HEADLINE_FONT,
				$line
			);
			$y += MESSAGE_LINE_HEIGHT;
		}
		// Save the image file
		imagegif($img, $fn);
		// Free resources
		imagedestroy($img);
	}
}

?>