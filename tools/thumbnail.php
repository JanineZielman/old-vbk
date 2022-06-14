<?php

/*
  Systemantics Image Resizer

  Copyright (C) 2006-2014 Systemantics, Bureau for Informatics

  Systemantics GmbH
  Bleichstr. 11
  41747 Viersen
  GERMANY

  Web:    www.systemantics.net
  Email:  hello@systemantics.net

  This file is NOT free software. Modification or usage of this file without
  buying an appropriate license from Systemantics breaks international
  copyright laws.
*/



// error_reporting(E_ALL ^ E_NOTICE);
// ini_set('display_errors', 1);

if (!defined('__DIR__')) {
	define('__DIR__', dirname(__FILE__));
}

define('THUMBNAIL_PATH', __DIR__.'/../cache/thumbnails/');
define('DEV_SERVER', substr($_SERVER['SERVER_NAME'], 0, 4) == 'dev.' || substr($_SERVER['SERVER_NAME'], 0, 9) == 'relaunch.' || substr($_SERVER['SERVER_NAME'], -6) == '.local' || substr($_SERVER['SERVER_NAME'], -4) == '.dev');
define('CONVERT', 'convert');
define('IMAGICK_TMP_DIR', __DIR__.'/../cache/tmp');

define('RESIZE_MODE_AUTO', 'auto');
define('RESIZE_MODE_GD', 'gd');
define('RESIZE_MODE_IM_PHP', 'im-php');
define('RESIZE_MODE_IM_EXEC', 'im-exec');

// Temp files for IMagick
putenv('MAGICK_TMPDIR=' . IMAGICK_TMP_DIR);
putenv('TMP=' . IMAGICK_TMP_DIR);
putenv('TMPDIR=' . IMAGICK_TMP_DIR);



// Settings
$resize_mode = RESIZE_MODE_AUTO;
$cache = true;
$quality = 90;



// From https://github.com/lencioni/SLIR/blob/master/core/slir.class.php
function getSharpeningMatrix($srcWidth, $srcHeight, $dstWidth, $dstHeight) {
	$final  = sqrt($dstWidth * $dstHeight) * (750.0 / sqrt($srcWidth * $srcHeight));
	$sharpnessFactor = max(round(52 - 0.27810650887573124 * $final + .00047337278106508946 * $final * $final), 0);

	return array(
		array(-1 / $sharpnessFactor, -2 / $sharpnessFactor, -1 / $sharpnessFactor),
		array(-2 / $sharpnessFactor, ($sharpnessFactor + 12) / $sharpnessFactor, -2 / $sharpnessFactor),
		array(-1 / $sharpnessFactor, -2 / $sharpnessFactor, -1 / $sharpnessFactor)
	);
}

// From http://php.net/manual/en/function.array-values.php, posted by geo dot artemenko at gmail dot com
function array_flatten($a, $f = array()) {
	if (!$a || !is_array($a)) {
		return false;
	}
	foreach($a as $k=>$v){
		if (is_array($v)) {
			$f = array_flatten($v,$f);
		} else {
			$f[$k] = $v;
		}
	}
	return $f;
}

/**
 * TRUE if any tag matched
 * FALSE if none matched
 * NULL if header is not specified
 */
function anyTagMatched($myTag) {
    $if_none_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ?
        stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) :
        false ;

    if( false !== $if_none_match ) {
        $tags = explode(', ', $if_none_match ) ;
        foreach( $tags as $tag ) {
            if( $tag == $myTag ) return true ;
        }
        return false ;
    }
    return null ;
}

function notModified() {
    header('HTTP/1.0 304 Not Modified');
    exit ;
}

//die(print_r($_GET, true));

// Sanitize parameters
$input = array(
	'file_name' => filter_input(INPUT_GET, 'file_name', FILTER_SANITIZE_STRING),
	'extension' => filter_input(INPUT_GET, 'extension', FILTER_SANITIZE_STRING),
	'w' => filter_input(INPUT_GET, 'w', FILTER_SANITIZE_NUMBER_INT),
	'h' => filter_input(INPUT_GET, 'h', FILTER_SANITIZE_NUMBER_INT),
	'c' => filter_input(INPUT_GET, 'c')=='true' ? 'true' : 'false',
	'g' => filter_input(INPUT_GET, 'g')=='true' ? 'true' : 'false',
);

if (!$input['file_name'] || !$input['extension'] || !$input['w'] || !$input['h']){
	echo "Missing required parameters";
	exit;
}

$outputExtension = strtolower($input['extension']);
if (!in_array($outputExtension, array('jpg', 'gif', 'png'))) {
	$outputExtension = 'jpg';
}

// Redirect if exists (Ideally should be in htaccess)
$file_check_path = THUMBNAIL_PATH . basename($input['file_name']) . '-' . $input['extension'] . '-' . $input['w'] . '-' . $input['h'] . '-' . $input['c'] . '-' . $input['g'] . '.' . $outputExtension;
$path = __DIR__ . '/../'. $input['file_name'].'.'.$input['extension'];

// Get image parent image
if (!file_exists($file_check_path) || filemtime($file_check_path) < filemtime($path)) {
	// Need to generate the image

	if (!file_exists($path)) {
		echo "Failed to find image";
		exit;
	}

	$filename = pathinfo($path, PATHINFO_FILENAME);
	$extension = pathinfo($path, PATHINFO_EXTENSION);

	list($width, $height, $type, $attr) = getimagesize($path);
	$parent_width = $new_width = $width;
	$parent_height = $new_height = $height;

	$width_max = $input['w'];
	$height_max = $input['h'];
	$crop = $input['c'];
	$grayscale = $input['g'];

	if ($resize_mode == RESIZE_MODE_AUTO) {
		// Auto-detect resize mode

		@exec(CONVERT . ' --version', $out, $returnCode);
		if ($out && ($returnCode == 0 || $returnCode == 1)) {
			// Get version
			if (preg_match('/ImageMagick (\d+\.\d+\.\d+(-\d+)?)/', $out[0], $matches)) {
				$imVersion = $matches[1];
			} else {
				$imVersion = FALSE;
			}
			$resize_mode = RESIZE_MODE_IM_EXEC;
		} else if (extension_loaded('imagick')) {
			$resize_mode = RESIZE_MODE_IM_PHP;
		} else if (extension_loaded('gd')) {
			$resize_mode = RESIZE_MODE_GD;
		} else {
			header('Content-Type: text/plain');
			echo 'Could not detect any useful resize mode.';
			exit;
		}
	}

	switch ($resize_mode) {
		case RESIZE_MODE_GD:
			// Load image
			switch($extension){
				case "jpg":
					$source = imagecreatefromjpeg($path);
					break;
				case "gif":
					$source = imagecreatefromgif($path);
					break;
				case "png":
					$source = imagecreatefrompng($path);
					break;
				default:
					echo "Failed image extension";
			}

			// If we load the image then continue
			if($source){
				// Rotate if EXIF information available and rotation necessary
				if (function_exists('exif_read_data')) {
					$exif = exif_read_data($path);
					if (is_array($exif) && isset($exif['Orientation'])) {
						switch ($exif['Orientation']) {
							case 8:
								// Tilt counter-clockwise
								$source = imagerotate($source, 90, 0);
								$dummy = $width;
								$width = $height;
								$height = $dummy;
								$parent_width = $new_width = $width;
								$parent_height = $new_height = $height;
								break;
							case 3:
								// Turn around
								$source = imagerotate($source, 180, 0);
								break;
							case 6:
								// Tilt clockwise
								$source = imagerotate($source, -90, 0);
								$dummy = $width;
								$width = $height;
								$height = $dummy;
								$parent_width = $new_width = $width;
								$parent_height = $new_height = $height;
								break;
						}
					}
				}

				if($crop == "true" && $width_max && $height_max){

					$test_height = $parent_height*($width_max/$parent_width);

					if($test_height > $height_max){
						$new_width = $width_max;
						$new_height = $parent_height*($width_max/$parent_width);
						$new_x = 0;
						$new_y = -1 * (($new_height - $height_max)/2);
					} else {
						$new_height = $height_max;
						$new_width = $parent_width*($height_max/$parent_height);
						$new_x = -1 * (($new_width - $width_max)/2);
						$new_y = 0;
					}

					// Create a blank image
					$new_image = imagecreatetruecolor($width_max,$height_max);

					// Resize or crop image
					imagecopyresampled($new_image, $source, $new_x, $new_y, 0, 0, $new_width, $new_height, $parent_width, $parent_height);

					// For database sizes
					$new_width = $width_max;
					$new_height = $height_max;

				} else {
					// Resize for width
					if ($width_max && ($new_width > $width_max)) {
						$new_height = $parent_height*($width_max/$parent_width);
						$new_width = $width_max;
					}

					// Adjust for height if need be
					if ($height_max && ($new_height > $height_max)) {
						$new_width = $parent_width*($height_max/$parent_height);
						$new_height = $height_max;
					}

					// Create a blank image
					$new_image = imagecreatetruecolor($new_width,$new_height);

					// Resize or crop image
					imagecopyresampled($new_image, $source, 0, 0, 0, 0, $new_width, $new_height, $parent_width, $parent_height);

				}

				if (function_exists('imageconvolution')) {
					// Sharpen
					imageconvolution(
						$new_image,
						getSharpeningMatrix($width, $height, $new_width, $new_height),
						1,
						0
					);
				}

				// Grayscale
				if ($grayscale == 'true') {
					imagefilter($new_image, IMG_FILTER_GRAYSCALE);
				}

				// Create it
				if (!is_dir(THUMBNAIL_PATH)) {
					@mkdir(THUMBNAIL_PATH);
				}
				switch ($outputExtension) {
					case 'jpg':
						imagejpeg($new_image, $file_check_path, $quality);
						break;
					case 'gif':
						imagegif($new_image, $file_check_path, $quality);
						break;
					case 'png':
						imagepng($new_image, $file_check_path, $quality);
						break;
				}

				// Tidy up
				imagedestroy($source);
				imagedestroy($new_image);

				// Make sure others (FTP users for instance) can handle the generated file
				chmod($file_check_path, 0666);
			} else {
				echo "Failed to load image";
				exit;
			}

			break;

		case RESIZE_MODE_IM_PHP:
			// Load image
			$im = new Imagick($path);

			switch ($image->getImageOrientation()) {
				case imagick::ORIENTATION_LEFTBOTTOM:
					$image->rotateimage("#000", -90); // rotate 90 degrees CCW
					$dummy = $width;
					$width = $height;
					$height = $dummy;
					$parent_width = $new_width = $width;
					$parent_height = $new_height = $height;
					break;
				case imagick::ORIENTATION_BOTTOMRIGHT:
					$image->rotateimage("#000", 180); // rotate 180 degrees
					break;
				case imagick::ORIENTATION_RIGHTTOP:
					$image->rotateimage("#000", 90); // rotate 90 degrees CW
					$dummy = $width;
					$width = $height;
					$height = $dummy;
					$parent_width = $new_width = $width;
					$parent_height = $new_height = $height;
					break;
			}

			if($crop == "true" && $width_max && $height_max){

				$new_width = $width_max;
				$new_height = $height_max;

				$ratio = max(
					$new_width / $parent_width,
					$new_height / $parent_height
				);

				// Crop image
				$dummy_width = $parent_width * $ratio;
				$dummy_height = $parent_height * $ratio;
				$im->resizeImage($dummy_width, $dummy_height, Imagick::FILTER_CATROM, 1);
				$im->extentImage($new_width, $new_height, -floor(($dummy_width - $new_width) / 2), -floor(($dummy_height - $new_height) / 2));
			} else {
				// Resize for width
				if ($width_max && ($new_width > $width_max)) {
					$new_height = $parent_height*($width_max/$parent_width);
					$new_width = $width_max;
				}

				// Adjust for height if need be
				if ($height_max && ($new_height > $height_max)) {
					$new_width = $parent_width*($height_max/$parent_height);
					$new_height = $height_max;
				}

				// Resize image
				$im->resizeImage($new_width, $new_height, Imagick::FILTER_CATROM, 1);
			}

			// Sharpen
			$im->convolveImage(array_flatten(getSharpeningMatrix($width, $height, $new_width, $new_height)));

			// Grayscale
			if ($grayscale == 'true') {
				$im->setImageColorspace(Imagick::COLORSPACE_GRAY);
			}

			// Create it
			if (!is_dir(THUMBNAIL_PATH)) {
				@mkdir(THUMBNAIL_PATH);
			}
			$im->setImageCompression(Imagick::COMPRESSION_JPEG);
			$im->setImageCompressionQuality($quality);
			$im->stripImage();
			$im->writeImage($file_check_path);

			// Tidy up
			$im->destroy();

			// Make sure others (FTP users for instance) can handle the generated file
			chmod($file_check_path, 0666);

			break;

		case RESIZE_MODE_IM_EXEC:
			if($crop == "true" && $width_max && $height_max){
				if ($imVersion >= '6.3.8-3') {
					// New fill given area with ^ resize flag
					$imParams = "-resize '$width_max" . "x$height_max^' -gravity center -extent $width_max" . "x$height_max";
				} else {
					// Old fill given area
					$doubleWidth = $width_max * 2;
					$doubleHeight = $height_max * 2;
					$imParams = "-resize x$doubleHeight -resize '$doubleWidth" . "x<' -resize 50% -gravity center -crop $width_max" . "x$height_max+0+0 +repage";
				}

				// For later use
				$new_width = $width_max;
				$new_height = $height_max;
			} else {
				$imParams = "-resize $width_max" . "x$height_max";
			}

			// Sharpen (if possible)
			if ($imVersion >= '6.5.9-0') {
				$sharpeningMatrix = getSharpeningMatrix($width, $height, $new_width, $new_height);
				$imParams = $imParams . " -morphology Convolve '3x3: " . implode(',', $sharpeningMatrix[0]) . ' ' . implode(',', $sharpeningMatrix[1]) . ' ' . implode(',', $sharpeningMatrix[2]) . "'";
			}

			// Grayscale
			if ($grayscale == 'true') {
				$imParams = $imParams . ' -colorspace gray';
			}

			// Optimize (animated) GIFs
			if ($outputExtension == 'gif') {
				$imParams = '-coalesce ' . $imParams;
			}

			// Create it
			$cmd = CONVERT . " '$path' -auto-orient $imParams -quality $quality '$file_check_path' 2>&1";
			exec($cmd, $out, $returnCode);
			if ($returnCode) {
				// An error occured, try to create the thumbnail path
				@mkdir(THUMBNAIL_PATH);
				// Make the thumbnail path readable
				@chmod(THUMBNAIL_PATH, 0777);

				// Retry
				unset($out);
				exec($cmd, $out, $returnCode);
				if ($returnCode) {
					header('Content-Type: text/plain');
					if (DEV_SERVER) {
						echo "An error occured while generating the thumbnail image.\n\nCommand: $cmd\n\nReturn code: $returnCode\n\n";
						foreach ($out as $ln) {
							echo "$ln\n";
						}
					} else {
						echo 'An error occured while generating the thumbnail image. Check the logs for what has happened.';
					}
					exit;
				}
			}

			// Make sure others (FTP users for instance) can handle the generated file
			chmod($file_check_path, 0666);

			break;
	}
}

$etag = hash_file('md5', $file_check_path);

if (anyTagMatched($etag)) {
	notModified();
}

// header('Expires:' . gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
header('ETag: ' . $etag);
header('Content-Type: image/jpeg');

readfile($file_check_path);
