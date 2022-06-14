<?php

function smarty_function_thumbnail($params, $template) {
    require_once(SMARTY_PLUGINS_DIR . 'shared.escape_special_chars.php');

    $alt = '';
    $file = '';
    $src = '';
    $height = FALSE;
    $width = FALSE;
    $crop = FALSE;
    $extra = '';
    $data = FALSE;

    $basedir = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '';
    if (substr($basedir, -1)=='/') {
    	$basedir = substr($basedir, 0, -1);
    }
    foreach($params as $_key => $_val) {
        switch ($_key) {
            case 'src':
            	$src = $_val;
            	break;

            case 'file':
            case 'height':
            case 'width':
                $$_key = $_val;
                break;

            case 'crop':
            case 'data':
            	$$_key = $_val==TRUE;
            	break;

            case 'alt':
                if (!is_array($_val)) {
                    $$_key = smarty_function_escape_special_chars($_val);
                } else {
                    throw new SmartyException ("thumbnail: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
                }
                break;

            default:
                if (!is_array($_val)) {
                    $extra .= ' ' . strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $_key)) . '="' . smarty_function_escape_special_chars($_val) . '"';
                } else {
                    throw new SmartyException ("thumbnail: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
                }
                break;
        }
    }

    if (empty($file)) {
    	$file = $src;
    }

    if (empty($src)) {
    	$src = $file;
    }

    if (empty($file)) {
        trigger_error("thumbnail: missing 'src' parameter", E_USER_NOTICE);
        return;
    }

    if (substr($file, 0, 1) == '/') {
        $_image_path = $basedir . $file;
    } else {
        $_image_path = $file;
    }

    $_image_path = preg_replace('/\?.*/', '', $_image_path);

    $placeholder = FALSE;

        if (!$_image_data = @getimagesize($_image_path)) {
            if (!file_exists($_image_path)) {
                if ($width && $height) {
                	// We can go ahead using a placeholder
                	$file = 'http://placehold.it/'.$width.'x'.$height;
                	$placeholder = TRUE;
                } else {
                	trigger_error("thumbnail: unable to find '$_image_path'", E_USER_NOTICE);
                }
            } else if (!is_readable($_image_path)) {
                trigger_error("thumbnail: unable to read '$_image_path'", E_USER_NOTICE);
                return;
            } else {
                trigger_error("thumbnail: '$_image_path' is not a valid image file", E_USER_NOTICE);
                return;
            }
        }

	if (!$placeholder) {
        if ($width && $height) {
        	// Both width and height are set, calc new width and height
        	if (!$crop) {
        		$ratio = min(
        			$width/$_image_data[0],
        			$height/$_image_data[1]
        		);
	        	$width = floor($_image_data[0] * $ratio);
	        	$height = floor($_image_data[1] * $ratio);
        	}
        } else if (!$width) {
        	// Calculate width according to height
        	$width = floor($_image_data[0] / $_image_data[1] * $height);
        } else {
        	// Calculate height according to width
        	$height = floor($_image_data[1] / $_image_data[0] * $width);
        }
	}

    return $prefix . '<img src="' . $src . '" alt="' . $alt . '" '.($data ? 'data-' : '').'width="' . $width . '" '.($data ? 'data-' : '').'height="' . $height . '"' . $extra . ' />' . $suffix;
}
