<?php
if (substr($_SERVER['SCRIPT_FILENAME'], -4)=='.css') {
	header('Content-Type: text/css');
} else {
	header('Content-Type: text/javascript');
}
ini_set('zlib.output_compression', 1);
