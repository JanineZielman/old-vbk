<?php

/*
	mogrify.inc
	Bildberechnungen mit ImageMagick/mogrify oder GD-Lib

	Copyright (C) 2003 by Pyrrhon Software GbR, Willich.

	Autor:	Lutz Ißler
	Datum:	17.02.2003

*/

if (!defined("_MOGRIFY_INCLUDED")) {

define("_MOGRIFY_INCLUDED",false);



define('MOGRIFY_ERR_OK', 0);
define('MOGRIFY_ERR_MOGRIFY', -1);
define('MOGRIFY_ERR_OPEN', -2);
define('MOGRIFY_ERR_CREATE', -3);
define('MOGRIFY_ERR_RESIZE', -4);
define('MOGRIFY_ERR_WRITE', -5);



// Konfiguration: Truecolor (nur ImageMagick und GD>=2.0) 
define('MOGRIFY_GD_SUPPORTS_TRUECOLOR', true);
define('MOGRIFY_AVAILABLE', false);



function convertGreyscale($image){
	imagetruecolortopalette($image, false, 256);
	$total = imageColorsTotal($image);
	for( $i=0; $i<$total; $i++) {
		$old = imageColorsForIndex($image, $i);
		$commongrey = (int)(($old[red] + $old[green] + $old[blue]) / 3);
		imageColorSet($image, $i, $commongrey, $commongrey, $commongrey);
	}
}



function mogrify($filename, $width, $height, $modifier = '') {
	if (MOGRIFY_AVAILABLE) {
		// Größen-String für Mogrify erstellen
		$size = $width.(strlen($height)>0 ? 'x'.$height : '').$modifier;
        // Bilddatei erstmal schreibbar machen
        @chmod($filename,0777) or FALSE;
        // Verzeichniswechsel (Workaround für eine buggy Version von mogrify)
        $old_cwd = getcwd();
        chdir(dirname($filename));
        exec(MOGRIFY." -geometry \"$size\" ".basename($filename));
        chdir($old_cwd);
	} else {
		// Mogrify gibt's nicht, weiche deshalb auf GD Library aus
		// Datei öffnen
		$srcImage = ImageCreateFromJPEG($filename);
		if (!$srcImage) {
			// Fehler beim Öffnen
			return MOGRIFY_ERR_OPEN;
		}
		// Größe ermitteln
		$srcWidth = ImageSX($srcImage);
		$srcHeight = ImageSY($srcImage);
		switch ($modifier) {
			case '>':
				// Nur resizen wenn größer als Vorgabe
				$resizeWidth = is_int($width) && ($srcWidth>$width);
				$resizeHeight = is_int($height) && ($srcHeight>$height);
				break;
			case '<':
				// Nur resizen wenn kleiner als Vorgaben
				$resizeWidth = is_int($width) && ($srcWidth<$width);
				$resizeHeight = is_int($height) && ($srcHeight<$height);
				break;
			default:
				// Immer resizen
				$resizeWidth = true;
				$resizeHeight = true;
				break;
		}
		// Überprüfen, ob width>height oder width<height
		// Entsprechend das Bild resizen, so dass es in die vorgegebe Größe passt
		$ratioHeight = $height>0 ? $srcHeight/$height : 0;
		$ratioWidth = $width>0 ? $srcWidth/$width : 0;
		if (!is_int($width)) {
			// Breite ist nicht angegeben, also die Höhe exakt treffen
			$destWidth = floor($srcWidth/$ratioHeight);
			$destHeight = $height;
			$doResize = $resizeHeight;
		} else if (!is_int($height)) {
			// Höhe ist nicht angegeben, also die Breite exakt treffen
			$destWidth = $width;
			$destHeight = floor($srcHeight/$ratioWidth);
			$doResize = $resizeWidth;
		} else {
			// Breite und Höhe als Maximalmaß verwenden 
			if ($ratioWidth<$ratioHeight) {
				$destWidth = floor($srcWidth/$ratioHeight);
				$destHeight = $height;
			} else {
				$destWidth = $width;
				$destHeight = floor($srcHeight/$ratioWidth);
			}
			$doResize = $resizeWidth || $resizeHeight;
		}
		if ($doResize) {
			// create the destination image with the new Width and Height
			if (MOGRIFY_GD_SUPPORTS_TRUECOLOR) {
				$destImage = ImageCreateTrueColor($destWidth, $destHeight);
			} else {
				$destImage = ImageCreate($destWidth, $destHeight);
			}
			if (!$destImage) {
				return MOGRIFY_ERR_CREATE;
			}
			//copy the srcImage to the destImage
			if (!ImageCopyResampled($destImage, $srcImage, 0, 0, 0, 0, $destWidth+1, $destHeight+1, $srcWidth, $srcHeight)) {
				return MOGRIFY_ERR_RESIZE;
			}
  			//create the image file
	  		if (!ImageJPEG($destImage, $filename)) {
				return MOGRIFY_ERR_WRITE;
			}
			//free the memory used for the dest image
			ImageDestroy($destImage);
		}
		//free the memory used for the src image
		ImageDestroy($srcImage);
	}
	return MOGRIFY_ERR_OK;
}


}