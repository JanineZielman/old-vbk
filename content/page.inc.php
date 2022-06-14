<?php

/*
  Content Management System

  Copyright (C) 2006?2008 Systemantics

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



require_once('constants.inc.php');



class page {

	var $sitename = false;
	var $config = false;
	var $lang = false;

	/**
	 * Create a page object.
	 *
	 * @param string $sitename
	 * @param Lang $lang
	 * @param Config $config
	 * @return Page
	 */
	function __construct($sitename, &$lang, &$config) {
		// Set variables
		$this->sitename = $sitename;
		$this->config = &$config;
		$this->lang = &$lang;

		// Diable caching
		header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s")." GMT");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		header('Content-type: text/html; charset=iso-8859-1');
	}

	/**
	 * Open a page. This creates the HTML head and opens the HTML body.
	 *
	 * @param string $title
	 * @param boolean $head defaults to false
	 * @param string $body Additional attributes for the <BODY> tag
	 */
	function open($title, $head = FALSE, $body = FALSE) {
		$this->page_preface($title, $body);

		if ($head) {
			$this->page_head($head);
		}
	}

	/**
	 * Print the page preface with doctype and head.
	 *
	 * @param string $title
	 * @param string $body
	 */
	function page_preface($title, $body = false) {
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
            "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
	<title><?php print $this->sitename; ?> - <?php print strip_tags($title); ?></title>
	<meta http-equiv="Expires" content="0" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link rel="stylesheet" href="configure.php?src=content.css&type=text/css" type="text/css" />
	<link rel="shortcut icon" href="elements/favicon.ico">
<?php
		if ($this->config) {
?>	<script language="javascript" type="text/javascript" src="tiny_mce/tiny_mce.js"></script>
	<script language="javascript" type="text/javascript">
	tinyMCE.init({
		apply_source_formatting : true,
		cleanup_on_startup : true,
		content_css : "editor_content.css,<?php print PATH_PREFIX.PATH_CONFIG.$this->config->value(CONFIG_CMS_SITE_CSS); ?>",
		editor_css : "../configure.php?src=editor_ui.css&type=text/css",
		editor_deselector : "plaintext",
		external_link_list_url : "links.php",
		language : "<?php print $this->lang->lang; ?>",
		mode: "textareas",
		plugins: "linebreak",
		popups_css : "configure.php?src=editor_popup.css",
		safari_warning : true,
		theme : "advanced",
		theme_advanced_blockformats : "<?php print $this->config->value(CONFIG_EDITOR_BLOCKFORMATS); ?>",
		theme_advanced_buttons1 : "<?php print $this->config->value(CONFIG_EDITOR_BUTTONS); ?>",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_align : "left",
		valid_elements : "<?php print $this->config->value(CONFIG_EDITOR_ELEMENTS); ?>"
	});
	</script>
<?php
		}
?>	<script language="javascript" type="text/javascript" src="dhtmlxColorPicker/dhtmlxcolorpicker.js"></script>
	<link rel="stylesheet" href="dhtmlxColorPicker/dhtmlxcolorpicker.css" type="text/css" />
</head>

<body<?php print $body ? " ".$body : ""; ?>>
<?php
	}

	/**
	 * Close the page. Prints out a footer if desired.
	 *
	 * @param boolean $foot defaults to true
	 */
	function close($foot = true) {
		if ($foot):
?><p id="footer"><?php print $this->getFooter(); ?></p><?php
		endif;
?></body>
</html>
<?php
	}

	/**
	 * Print a page headline enclosed in an <h1> tag.
	 *
	 * @param string $text
	 */
	function page_head($text) {
		$logo = $this->config->value(CONFIG_CMS_LOGO, false);
		print '<h1>'.($logo ? '<img src="'.PATH_PREFIX.PATH_CONFIG.$logo.'" alt="'.$this->sitename.'">' : '').$text."</h1>\n";
	}

	/**
	 * Returns the footer string.
	 *
	 * @return string
	 */
	function getFooter() {
		return '<a href="javascript:window.open(\'about.php\',\'cmsabout\',\'width=500,height=550,left=200,top=50,directories=0,location=0,menubar=0,resizable=1,scrollbars=1,status=0,titlebar=1,toolbar=0\').focus()" title="'.$this->lang->l('showabout').'">'.CMS_LOGO.' '.$this->lang->l('cms').'</a> '.$this->lang->l('of').' '.$this->sitename.'. Copyright &copy; 2006&#150;2008 by <a href="http://www.systemantics.net/" target="_blank" title="'.$this->lang->l('showpyrrhon').'">Systemantics, Bureau for Informatics</a>.';
	}

	/**
	 * Print out a complete page with an error message.
	 *
	 * @param string $msg First paragraph of the error message.
	 * @param string $moreMsg Second paragraph of the error message.
	 * @param string $backUrl The URL to jump to when the user hits
	 * the OK button. If this is not specified no button is printed out.
	 */
	function error($msg, $moreMsg = false, $backUrl = false) {
		$this->open($this->lang->l('error.error'), $this->lang->l('error.error'));
		$this->p($msg);
		if ($moreMsg) {
			$this->p($moreMsg);
		}
		if ($backUrl) {
			$this->p($this->button(BUTTON_OK, $this->lang->l('back'), $backUrl));
		}
		$this->close();
	}

	// Internal: current table row
	var $table_act_row = 0;
	var $table_num_cols = 0;

	/**
	 * Starts a table. The defs parameter is an array where the even indices
	 * contain the column titles and the odd indices contain the colums widths.
	 *
	 * @param array $defs
	 * @param string $class CSS class of the table
	 */
	function table_head($defs, $class = false) {
		echo "<table".($class ? ' class="'.$class.'"' : '')." summary=\"\">\n<thead>\n<tr>\n";
		for ($i=0; $i<count($defs)/2; $i++) {
			if ($i==0) {
				$class = ' class="first"';
			} else if ($i==count($defs)-1) {
				$class = ' class="last"';
			} else {
				$class = '';
			}
			echo "\t<th scope=\"col\"".(($defs[$i*2+1]>0) ? " style=\"width:".$defs[$i*2+1]."px;\"" : "").$class.'>'.$defs[$i*2]."</th>\n";
		}
		echo "</tr>\n";
		echo "</thead>\n";
		echo "<tbody>\n";
		$this->table_act_row = 0;
	}

	/**
	 * Prints a table row. The defs array contains the strings that
	 * should be filled in the table columns.
	 *
	 * @param array $defs
	 * @param string $title The title attribute value for that row.
	 * @param string $onclick The onclick action for that row.
	 */
	function table_row($defs, $title = false, $onclick = false, $additional = false) {
		echo '<tr'.(($this->table_act_row%2) ? " class=\"even\"" : " class=\"odd\"").($title ? ' title="'.$title.'"' : '').($onclick ? ' onclick="'.$onclick.'"' : '').($additional ? ' '.$additional : '').">\n";
		for ($i=0; $i<count($defs); $i++) {
			if ($i==0) {
				$class = ' class="first"';
			} else if ($i==count($defs)-1) {
				$class = ' class="last"';
			} else {
				$class = '';
			}
			echo "\t<td".$class.'>'.$defs[$i]."</td>\n";
		}
		echo "</tr>\n";
		$this->table_act_row++;
	}

	/**
	 * Closes the table.
	 */
	function table_foot() {
		echo "</tbody>\n";
		echo "</table>\n";
	}

	/**
	 * Prints out an "Refresh:" HTML header with the specified URL.
	 *
	 * @param string $url
	 */
	function redirect_url($url) {
		header('Refresh: 0;URL='.html_entity_decode($url));
	}

	/**
	 * Returns the HTML code for a button.
	 *
	 * @param string $type
	 * @param string $text
	 * @param string $url
	 * @return string
	 */
	function button($type, $text, $url = '') {
		if ($url=='') {
			// Submit button
			$action = 'type="submit"';
		} else {
			// Navigation button
			$action = 'onClick="location.href=\''.$url.'\';return false;"';
		}
		return '<button class="button" '.$action.' title="'.$text.'">'
			.'<img src="elements/'.$type.'_'.$this->lang->lang.'.gif" alt="'.$text.'"></button>';
	}

	/**
	 * Returns the HTML code for a text input field.
	 *
	 * @param string $name
	 * @param string $value
	 * @param integer $size
	 * @param string $add
	 * @return string
	 */
	function input_text($name, $value, $size = 30, $add = '') {
		if ($size<=10) {
			$class = "narrow";
		} else if ($size<=20) {
			$class = "medium";
		} else {
			$class = "wide";
		}
		return "<input type=\"text\" name=\"$name\" class=\"$class\" value=\"$value\"$add />";
	}

	/**
	 * Returns the HTML code for a password input field.
	 *
	 * @param string $name
	 * @param integer $size
	 * @return string
	 */
	function input_password($name, $size = 50) {
		if ($size<=10) {
			$class = "narrow";
		} else if ($size<=20) {
			$class = "medium";
		} else {
			$class = "wide";
		}
		return "<input type=\"password\" name=\"$name\" class=\"$class\"$add />";
	}

	/**
	 * Returns the HTML code for a memo text field.
	 *
	 * @param string $name
	 * @param string $value
	 * @param integer $size
	 * @param string $class
	 * @return string
	 */
	function input_memo($name, $value, $size = 15, $class = 'text') {
		return "<textarea name=\"$name\" class=\"$class\" rows=\"$size\">$value</textarea>";
	}

	/**
	 * Return the HTML code for a picture input field.
	 *
	 * @param string $name
	 * @param string $imgFilename
	 * @param string $deleteUrl
	 * @return string
	 */
	function input_picture($name, $imgFilename, $deleteUrl = false) {
		$del = $this->input_checkbox('del'.$name, $this->lang->l('deleteimage'));
		return ($imgFilename ? "<img class=\"inputPicture\" src=\"$imgFilename\" border=\"1\" alt=\"\">&nbsp;$del<br>\n".$this->lang->l('newimage').": " : "")."<input type=\"file\" name=\"$name\" accept=\"image/jpeg\" size=\"50\">".($imgFilename ? "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(".$this->lang->l('newimagenote').")" : "");
	}

	/**
	 * Return the HTML code for a file input field.
	 *
	 * @param string $name
	 * @param string $filename
	 * @param string $deleteUrl
	 * @return string
	 */
	function input_file($name, $filename, $deleteUrl = false) {
		$del = $this->input_checkbox('del'.$name, $this->lang->l('deletefile'));
		return ($filename ? "<a href=\"$filename\">".rawurldecode(basename($filename))."</a>&nbsp;$del<br>\n".$this->lang->l('newfile').": " : "")."<input type=\"file\" name=\"$name\" size=\"50\">".($filename ? "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(".$this->lang->l('newfilenote').")" : "");
	}

	/**
	 * Starts an HTML form.
	 *
	 * @param string $onSubmit
	 */
	function form_head($onSubmit = false) {
		print '<form name="content" method="post" action="'.SCRIPTNAME.'" enctype="multipart/form-data"'.($onSubmit ? ' onsubmit="'.$onSubmit.'"' : '').'>'."\n";
	}

	/**
	 * Closes an HTML form.
	 *
	 * @param string $cancelUrl
	 */
	function form_foot($cancelUrl = '') {
		print '<p class="buttons">'.$this->button(
			$cancelUrl===false ? BUTTON_OK : BUTTON_SAVE,
			$cancelUrl===false ? $this->lang->l('okbutton') : $this->lang->l('savebutton'))
			.($cancelUrl===false ? '' : '&nbsp;&nbsp;'.$this->button(BUTTON_CANCEL, $this->lang->l('cancelbutton'), $cancelUrl))
			."</p>\n";
		print "</form>\n";
?><script language="javascript" type="text/javascript">
for (i=0; i<document.content.elements.length; i++) {
	if (document.content.elements[i].type!="hidden") {
		document.content.elements[i].focus();
		if (document.content.elements[i].type=="text") {
			document.content.elements[i].select();
		}
		break;
	}
}
</script>
<?php
	}

	/**
	 * Prints an OK button.
	 */
	function ok_button($url = false) {
		print '<p class="buttons">'.$this->button(
				BUTTON_OK,
				$this->lang->l('okbutton'),
				$url
			)."</p>\n";
	}

	/**
	 * Prints out a hidden form field.
	 *
	 * @param string $name
	 * @param string $value
	 */
	function hidden($name, $value) {
		print "<input type=\"hidden\" name=\"$name\" value=\"$value\">\n";
	}

	/**
	 * Prints out a paragraph with the specified text content.
	 *
	 * @param string $s
	 */
	function p($s) {
		print "<p>$s</p>\n";
	}

	/**
	 * Prints a note that uploading files might take some time.
	 */
	function upload_msg() {
		?><p class="message"><strong><?php print $this->lang->l('hint'); ?>:</strong> <?php print $this->lang->l('uploadmsg'); ?><br>
<br>
<?php
		print $this->input_checkbox(PARAM_HIDE_UPLOAD_MSG, $this->lang->l('dontshowthishint'))."\n";
?></p>
<?php
	}

	/**
	 * Prints out a general hint.
	 *
	 * @param string $text
	 */
	function hint($text) {
		?><p class="message"><strong><?php print $this->lang->l('hint'); ?>:</strong> <?php print $this->lang->l('uploadmsg'); ?></p>
<?php
	}

	/**
	 * Returns the HTML code for a checkbox.
	 *
	 * @param string $name
	 * @param string $caption
	 * @param string $value
	 * @return string
	 */
	function input_checkbox($name, $caption, $value = false) {
		return '<input type="checkbox"'.($value ? ' checked="checked"' : '').' class="checkbox" id="content'.$name.'" name="'.$name.'" value="1"><label for="content'.$name.'">'.$caption.'</label>';
	}

	/**
	 * Returns the HTML code for a group of radiobuttons.
	 *
	 * @param string $name
	 * @param array $options
	 * @param string $value
	 * @return string
	 */
	function input_radio($name, $options, $value) {
		$radio = '';
		if (!$value) {
			// Default selection is first value
			$value = @reset($options);
		}
		foreach ($options as $caption=>$val) {
			$radio .= "<input type=\"radio\" class=\"radio\" name=\"$name\" id=\"content$name$val\" value=\"$val\"".($val==$value ? ' checked="checked"' : '')."><label for=\"content$name$val\">$caption</label><br>\n";
		}
		return $radio;
	}

	/**
	 * Returns the HTML code of a selection box or combobox (depending
	 * on the specified size).
	 *
	 * @param string $name
	 * @param array $options
	 * @param string $value
	 * @param integer $size
	 * @param boolean $multiple
	 * @param string $onchange
	 * @return string
	 */
	function input_select($name, $options, $value, $size = 1, $multiple = false, $onchange = '') {
		$select = "<select name=\"$name\" size=\"$size\"".($multiple ? ' multiple' : '').' onchange="'.$onchange."\">\n";
		$value = explode(',', $value);
		foreach ($options as $val=>$caption) {
			$select .= "\t<option value=\"$val\"".(array_search($val, $value)===FALSE ? '' : ' selected="selected"').">$caption</option>\n";
		}
		return $select.'</select>';
	}

	/**
	 * Return a link to SCRIPTNAME with the specified URL parameters.
	 *
	 * @param array $params
	 * @param boolean $plain
	 * @return string
	 */
	function link($params = array(), $plain = false) {
		$url = '';
		foreach ($params as $key=>$value) {
			$url .= ($plain ? '&' : '&amp;').$key.'='.$value;
		}
		return SCRIPTNAME.'?'.substr($url, ($plain ? 1 : 5));
	}

}	// class page



?>
