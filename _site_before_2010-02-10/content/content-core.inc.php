<?php

/*
  Content Management System
  van Bergen Kolpa

  Copyright (C) 2006 Pyrrhon Software GbR

  Lutz und Arnd Iﬂler GbR
  Paul-Klee-Str. 54
  47877 Willich
  GERMANY

  Web:    www.pyrrhon.com
  Email:  info@pyrrhon.com

  Permission granted to use this file only on the server of van Bergen Kolpa.
*/



require_once('config.inc.php');



class _page {



	function _page() {
	}



	function open($title, $head = FALSE, $body = FALSE) {
		// Diable caching
		header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s")." GMT");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");

		// Start session
		session_name(LOGO_PLAIN);
		session_start();

		// Request login
  		if (isset($_REQUEST['login'])) {
  			// Userdaten in Session speichern
  			$_SESSION['username'] = $_REQUEST['username'];
  			$_SESSION['password'] = $_REQUEST['password'];
  		}

  		if (($_SESSION['username']!=CMS_USERNAME)
  			|| ($_SESSION['password']!=CMS_PASSWORD)) {
  			// Login data not entered yet or wrong
  			// Perform authentication
  			$this->page_preface("Authentication");
  			$this->page_head("Login");
				print "<p>You have to login in order to modify the content of your website.</p>\n";
				print "<p>Please enter the login data for the content management system:</p>\n";
  			$this->form_head();
  			$this->hidden('login',1);
  			$this->table_head(array('Authentication',0,'',0));
  			$this->table_row(array('Username:', $this->input_text('username','',10)));
  			$this->table_row(array('Password:', $this->input_password('password',10)));
  			$this->table_foot();
  			$this->form_foot('username', 'javascript:window.close()', CMS_BUTTON_OK);
  			$this->close();
  			exit;
  		}

  		if (isset($_REQUEST['login'])) {
  			// Logindaten waren ok, jetzt zur aufgerufenen Seite weiterleiten
  			$this->redirect_url(SCRIPTNAME);
				exit;
  		}

		$this->page_preface($title, $body);

		if ($head) {
			$this->page_head($head);
		}
	}



	function page_preface($title, $body = FALSE) {
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
	<title><?php print LOGO_PLAIN; ?> &#150; <?php print $title; ?></title>
	<meta http-equiv="Expires" content="0" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="content.css" rel="stylesheet" type="text/css" />
</head>

<body<?php print $body ? " ".$body : ""; ?>>
<?php
	}



	function close($foot = true) {
		if ($foot):
?><p class="small"><?php print LOGO_PLAIN; ?> Content Management. Copyright &copy; 2006 by <a href="http://www.pyrrhon.com/" target="_new">Pyrrhon Software GbR</a>.</p><?php
		endif;
?></body>
</html>
<?php
	}



	function page_head($text) {
		print "<h1>".LOGO." Content Management &#150; $text</h1>\n";
	}



	// Internal: current table row
	var $table_act_row = 0;
	var $table_num_cols = 0;

	function table_head($defs) {
		$space = 10;
		$this->table_act_row = 0;
		echo "<div><table cellpadding=\"1\" cellspacing=\"0\" border=\"0\" class=\"table_head\">\n<tr>\n<td>\n";
		echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" align=\"center\">\n<tr>\n";
		echo "<td><img src=\"blank.gif\" width=\"5\" height=\"1\" border=\"0\"></td>\n";
		for ($i=0; $i<count($defs)/2; $i++) {
			echo "\t<td>".(($defs[$i*2+1]>0) ? "<img src=\"blank.gif\" width=".$defs[$i*2+1]." height=\"1\" border=\"0\">" : "")."</td>\n";
			if ($i<count($defs)/2-1)
				echo "\t<td><img src=\"blank.gif\" width=\"$space\" height=\"1\" border=\"0\"></td>\n";
		}
		echo "<td><img src=\"blank.gif\" width=\"5\" height=\"1\" border=\"0\"></td>\n";
		echo "</tr>\n<tr class=\"table_head\">\n";
		echo "<td></td>\n";
		for ($i=0; $i<count($defs)/2; $i++) {
			echo "\t<th align=\"left\"><span class=\"table_head_text\">".$defs[$i*2]."</font></th>\n";
			if ($i<count($defs)/2-1)
				echo "\t<th>&nbsp;</th>\n";
		}
		echo "<td></td>\n";
		echo "</tr>\n<tr>\n";
		echo "\t<td class=\"table_white\" colspan=".(count($defs)+1)."><img src=\"blank.gif\" width=\"1\" height=\"$space\" border=\"0\"></td>\n";
		echo "</tr>\n";
	}



	function table_row($defs) {
		echo "<tr".((++$this->table_act_row%2) ? " class=\"table_row\"" : " class=\"table_white\"").">\n";
		echo "<td><img src=\"blank.gif\" width=\"1\" height=\"1\" border=\"0\"></td>\n";
		for ($i=0; $i<count($defs); $i++) {
			echo "\t<td align=\"left\" valign=\"top\">".
				(($defs[$i]=='') ? "<img src=\"blank.gif\" width=\"1\" height=\"1\" border=\"0\">" : $defs[$i])."</td>\n";
			if ($i<count($defs)-1)
				echo "\t<td>&nbsp;</td>\n";
		}
		echo "<td><img src=\"blank.gif\" width=\"1\" height=\"1\" border=\"0\"></td>\n";
		echo "</tr>\n";
	}



	function table_foot() {
		echo "</table>\n";
		echo "</tr>\n</td>\n</table></div>\n";
	}



	function redirect_url($url) {
		header("Refresh: 0;URL=$url");
	}



	/**
	 * Print out a button
	 */
	function button($type,$text,$url = "") {
		return '<button class="button" '
			.((($type==BUTTON_SAVE) || ($type==BUTTON_OK)|| ($type==BUTTON_YES)) && ($url=="") ?
				'type="submit"' :
				'type="button" onClick="location.href=\''.$url.'\'"')
			.'><img src="'.$type.'.gif" alt="'.$text.'" border="0" /></button>';
	}



	/**
	 * Return a text field.
	 */
	function input_text($name, $value, $size = 30, $add = '') {
		return "<input type=\"text\" name=\"$name\" value=\"$value\" size=\"$size\" style=\"width:".($size*12)."px\"$add />";
	}



	/**
	 * Return a password field.
	 */
	function input_password($name, $size = 50) {
		return '<input type="password" name="'.$name.'" size="'.$size.'" style="width:'.($size*12).'">';
	}



	/**
	 * Return a text area.
	 */
	function input_memo($name, $value, $size = 15) {
		return "<textarea name=\"$name\" cols=\"80\" rows=\"$size\">$value</textarea>";
	}



	// Formular beginnen
	function form_head() {
		print '<p><form name="content" method="post" action="'.SCRIPTNAME.'" enctype="multipart/form-data">'."\n";
	}



	// Formular schlieﬂen
	function form_foot($focus = false, $cancelUrl = SCRIPTNAME) {
		print '<p>'.$this->button(BUTTON_SAVE, 'Save changes').'&nbsp;&nbsp;'
			.$this->button(BUTTON_CANCEL, 'Discard changes', $cancelUrl)
			."</p>\n"
			."</form></p>\n"
			.($focus ? "<script language=\"JavaScript\">\n"
				."document.content.$focus.focus();\n"
				."</script>\n" : '');
	}



	// Hidden-Feld ausgeben
	function hidden($name, $value) {
		print "<input type=\"hidden\" name=\"$name\" value=\"$value\">\n";
	}



}	// class _page

$page = new _page();



?>
