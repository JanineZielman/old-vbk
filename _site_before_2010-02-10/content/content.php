<?php

/*
  Content Management System
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



require_once('content-core.inc.php');
require_once('database.inc.php');
require_once('mogrify.inc.php');
require_once('../vbk-core.inc.php');



define('SCRIPTNAME', 'content.php');



// Start session
session_start('redaktion');
if (!isset($_SESSION['expanded'])) {
	$_SESSION['expanded'] = array();
}




// Evaluate expanding/collapsing
if ($_REQUEST['action']=='expand') {
	if (in_array($_REQUEST['id'], $_SESSION['expanded'])) {
		// Collapse
		array_splice(
			$_SESSION['expanded'],
			array_search($_REQUEST['id'], $_SESSION['expanded']),
			1
		);
	} else {
		// Expand
		$_SESSION['expanded'][] = $_REQUEST['id'];
	}
	// Back to list
	$page->redirect_url(SCRIPTNAME.'#'.$_REQUEST['id']);
	exit;
}








// Move article by $delta positions
// (warning: no checking is done wether $delta makes sense)
function move_article($db, $id, $delta) {
	// Retrieve article position and parent
	$query = 'SELECT ord,parent FROM articles WHERE id='.$_REQUEST['id'];
	$db->query($query);
	$db->next_row();

	// Calculate the new position
	$newOrd = $db->row->ord+$delta;
	$parent = $db->row->parent;

	// Move the article to the new position
	$query = 'UPDATE articles SET ord='.$newOrd.' WHERE id='.$id;
	$db->simplequery($query);

	// Move the article that was at the new position to the old position
	$query = 'UPDATE articles SET ord='.$db->row->ord.' WHERE parent='.$parent.' AND ord='.$newOrd.' AND id!='.$id;
	$db->simplequery($query);
}




if ($_REQUEST['action']=='list'):

	// Print list with artice IDs
	$query = 'SELECT id,title_nl AS title FROM articles ORDER BY title';
	$db->query($query);
	$page->open('Artikel-IDs');
	$page->table_head(array('Article ID', 50, 'Title', 0));
	while ($db->next_row()) {
		$page->table_row(array($db->row->id, $db->row->title));
	}
	$page->table_foot();
	$page->close();
	exit;









elseif ($_REQUEST['action']=='up'):

	// Move article one position up
	move_article($db, $_REQUEST['id'], -1);

	// Back to list
	$page->redirect_url(SCRIPTNAME);
	exit;









elseif ($_REQUEST['action']=='down'):

	// Move article one position up
	move_article($db, $_REQUEST['id'], 1);

	// Back to list
	$page->redirect_url(SCRIPTNAME);
	exit;









elseif ($_REQUEST['action']=='catup'):

	// Get the current parent
	$query = "SELECT ord,parent FROM articles WHERE id='".$_REQUEST['id']."'";
	$db->query($query);
	$db->next_row();
	$ord = $db->row->ord;
	$parent = $db->row->parent;

	// Get the new parent
	$query = "SELECT ord,parent FROM articles WHERE id='$parent'";
	$db->query($query);
	$db->next_row();
	$query = "SELECT id FROM articles WHERE parent='".$db->row->parent."' AND ord='".($db->row->ord-1)."'";
	$db->query($query);
	$db->next_row();
	$newParent = $db->row->id;

	// Get the number of entries under the new parent
	$query = "SELECT COUNT(id) AS number FROM articles WHERE parent='$newParent'";
	$db->query($query);
	$db->next_row();
	$newPosition = $db->row->number+1;

	// Change the parent of current article and set position to last position
	$query = "UPDATE articles SET ord='$newPosition',parent='$newParent' WHERE id='".$_REQUEST['id']."'";
	$db->simplequery($query);

	// Number all articles below the old child one position down
	$query = "UPDATE articles SET ord=ord-1 WHERE ord>".$ord." AND parent=$parent";
	$db->simplequery($query);

	// Back to list
	$page->redirect_url(SCRIPTNAME);
	exit;









elseif ($_REQUEST['action']=='catdown'):

	// Get the current parent
	$query = "SELECT ord,parent FROM articles WHERE id='".$_REQUEST['id']."'";
	$db->query($query);
	$db->next_row();
	$ord = $db->row->ord;
	$parent = $db->row->parent;

	// Get the new parent
	$query = "SELECT ord,parent FROM articles WHERE id='$parent'";
	$db->query($query);
	$db->next_row();
	$query = "SELECT id FROM articles WHERE parent='".$db->row->parent."' AND ord='".($db->row->ord+1)."'";
	$db->query($query);
	$db->next_row();
	$newParent = $db->row->id;

	// Number all articles below the new child one position up
	$query = "UPDATE articles SET ord=ord+1 WHERE parent=$newParent";
	$db->simplequery($query);

	// Change the parent of current article and set position to last position
	$query = "UPDATE articles SET ord='1',parent='$newParent' WHERE id='".$_REQUEST['id']."'";
	$db->simplequery($query);

	// Back to list
	$page->redirect_url(SCRIPTNAME);
	exit;









elseif ($_REQUEST['action']=='delete'):

	// Delete article and all sub-articles

	// Recursively delete articles
	function delete_article($db, $id) {
		// Retrieve child articles from the database
		$query = "SELECT id FROM articles WHERE parent=$id";
		$db->query($query);
		while ($db->next_row()) {
			// Delete child article
			delete_article(new database(), $db->row->id);
		}
		// Delete all images of that article
		for ($i=1; $i<=NUM_PICTURES; $i++) {
			@unlink(PATH_PREFIX.IMAGE_PATH.'article'.sprintf('%06d-%02d', $id, $i).IMAGE_SUFFIX);
		}
		// Delete all headlines of this article
		if ($handle = opendir(PATH_PREFIX.HEADLINE_PATH)) {
			while (false !== ($file = readdir($handle))) {
				$fn = headlineBasename($id);
				if (strpos($file, $fn)!==false) {
					@unlink(PATH_PREFIX.HEADLINE_PATH.$file);
				}
			}
			closedir($handle);
		}
		// Get the article's position
		$query = "SELECT ord,parent FROM articles WHERE id=$id";
		$db->query($query);
		$db->next_row();
		$ord = $db->row->ord;
		$parent = $db->row->parent;
		// Delete the article itself
		$query = "DELETE FROM articles WHERE id=$id";
		$db->simplequery($query);
		// Move all other articles one article up
		$query = "UPDATE articles SET ord=ord-1 WHERE ord>".$ord." AND parent=$parent";
		$db->simplequery($query);
	}

	// Call deletion function
	delete_article($db, $_REQUEST['id']);

	// Back to list
	$page->redirect_url(SCRIPTNAME);
	exit;









elseif ($_REQUEST['action']=='edit'):

	// Edit article

	$id = $_REQUEST['id'];

	if ($id==ID_NEW) {
		// Create new article
		$page->open('Article', 'New Article');
		unset($db->row);
		$parent = $_REQUEST['parent'];
	} else {
		// Get article from database
		$page->open('Article', 'Edit Article');

		$query = "SELECT * FROM articles WHERE (id='$id')";
		$db->query($query);
		$db->next_row();
		$parent = $db->row->parent;
	}

	// Retrieve the parent articles
	function article_path($db, $id) {
		if ($id==0) {
			return LOGO_PLAIN;
		}
		$query = "SELECT parent,title_nl AS title FROM articles WHERE id=$id";
		$db->query($query);
		$db->next_row();
		$title = htmlentities(stripslashes($db->row->title));
		return article_path($db, $db->row->parent).' -> '.$title;
	}

	print '<p>Parent article: <strong>'.article_path(new database(), $parent)."</strong></p>\n";
	if ($_REQUEST['id']!=ID_NEW) {
		print '<p>Internal ID of this article: <strong>'.$_REQUEST['id']."</strong></p>\n";
	}

	$page->form_head();

	$page->hidden('dummy', 'nothing');
	$page->hidden('id', $id);
	$page->hidden('action', 'save');
	$page->hidden('parent', $parent);
	$page->hidden('order', $_REQUEST['order']);

	$page->table_head(array('Article', 100, '', 0));
	foreach ($LANG as $language=>$name) {
		$page->table_row(array("Title $name:", $page->input_text("title_$language", htmlentities(stripslashes($db->row->{"title_$language"})))));
	}

	for ($i=1; $i<=NUM_PICTURES; $i++) {
		$picfilename = PATH_PREFIX.IMAGE_PATH.'article'.sprintf('%06d-%02d', $id, $i).IMAGE_SUFFIX;
		if (!file_exists($picfilename)) {
			$picfilename = false;
		}
		$del = '<input type="checkbox" name="delpic'.$i.'" value="1">delete image</input>';
		$input = ($picfilename ? "<img src=\"$picfilename\" height=\"100\" border=\"1\">&nbsp;$del<br>\nNew: " : "")."<input type=\"file\" name=\"pic$i\" accept=\"image/jpeg\" size=\"50\">".($picfilename ? "<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(fill only if you want to upload a new image)" : "");
		$page->table_row(array("Image".(NUM_PICTURES>1 ? " $i" : '')." (JPEG):", $input));
	}
	foreach ($LANG as $language=>$name) {
		$page->table_row(array(
			"Text $name:",
			$page->input_memo("text_$language", $db->row->{"text_$language"}, 20)
		));
	}
	$page->table_row(array(
		'Formattings:',
		'<code>== Headline == (horizontal rule included. Use \\\\ for linebreaks!)<br />'
		.'//gray text//<br />'
		.'--- horizontal rule<br />'
		.'[Internet link]<br />'
		.'[Internet link|displayed text]<br />'
		.'[[article ID]]<br />'
		.'[[article id|displayed text]] &#150; <a href="javascript:window.open(\''.SCRIPTNAME."?action=list','idlist','width=500,height=400,directories=0,location=0,menubar=0,resizable=1,scrollbars=1,status=0,titlebar=1,toolbar=0').focus();\">list of available article IDs</a>"
	));
	$page->table_foot();

	$page->form_foot('headline');

	$page->close();
	exit;









elseif ($_REQUEST['action']=='save'):

	// Save article
	$isNew = $_REQUEST['id']==ID_NEW;
	if ($isNew) {
		$_REQUEST['id'] = $db->new_id();
	}

	$query = '';
	$title = false;

	foreach ($LANG as $language=>$name) {
		// Delete all existing headlines for this article and language
		if (!$isNew) {
			$query2 = "SELECT text_$language AS text FROM articles WHERE id=".$_REQUEST['id'];
			$db->query($query2);
			$db->next_row();
			preg_match_all('/==\s*(.*)\s*==/', stripslashes($db->row->text), $heads);
			foreach ($heads[1] as $headline) {
				// Delete headline
				$headline = trim($headline);
				@unlink(PATH_PREFIX.headlineFilename($_REQUEST['id'], $headline));
			}
		}

		// Generate the headlines for this article and language
		preg_match_all('/==\s*(.+)\s*==/', $_REQUEST["text_$language"], $heads);
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

		if (!$title) {
			// Store first title
			$title = $_REQUEST["title_$language"];
		} else if (trim($_REQUEST["title_$language"])=='') {
			// If a title is empty, use the first title
			$_REQUEST["title_$language"] = $title;
		}

		$query .= ",title_$language='".addslashes($_REQUEST["title_$language"])."',"
			."text_$language='".addslashes($_REQUEST["text_$language"])."'";
	}
	$query = substr($query, 1);

	// Save inline images
	for ($i=1; $i<=NUM_PICTURES; $i++) {
		$fn = PATH_PREFIX.IMAGE_PATH.'article'.sprintf('%06d-%02d', $_REQUEST['id'], $i).IMAGE_SUFFIX;
		if ($_REQUEST["delpic$i"]==1) {
			// Delete picture
			@unlink($fn);
		} else {
			// Update picture
	  		if (($_FILES["pic$i"]['error']==0) && ($_FILES["pic$i"]['size']>0)) {
				// Delete old image
				@unlink($fn);
  				// Copy new image
  				move_uploaded_file($_FILES["pic$i"]['tmp_name'], $fn);
				chmod($fn, 0644);
				// Resize image
  				$size = GetImageSize($fn);
				if ($size[0]>IMAGE_WIDTH) {
    				$width = IMAGE_WIDTH;
	    			$height = $size[1]/$size[0]*$width;
    	  			mogrify($fn, $width, $height);
				}
			}
		}
	}

	if ($isNew) {
		// New article
		// Check wether a position was explicitly specified
		if ($_REQUEST['order']=='') {
			// No position specified, create article as last article
	    	// Get number of articles with the same parent
   			$query2 = 'SELECT COUNT(*) AS cnt FROM articles WHERE parent='.$_REQUEST['parent'];
			$db->query($query2);
	    	$db->next_row();
			$cnt = $db->row->cnt;
    		$ord = $cnt+1;
		} else {
			// Position specified
			// Make room at this position
	   		$query2 = 'UPDATE articles SET ord=ord+1 WHERE parent='.$_REQUEST['parent'].' AND ord>='.$_REQUEST['order'];
			$db->query($query2);
    		$db->next_row();
			$cnt = $db->row->cnt;
			// Create article at this position
			$ord = $_REQUEST['order'];
		}
		// Get a new id and save article
		$query = "INSERT INTO articles SET $query,ord=$ord,parent=".$_REQUEST['parent'].",id=".$_REQUEST['id'];
	} else {
		// Existing article
		$query = "UPDATE articles SET $query WHERE id=".$_REQUEST['id'];
	}
	$db->simplequery($query);

	// Back to list
	$page->redirect_url(SCRIPTNAME);
	exit;


















elseif ($_REQUEST['action']=='recent'):

	// Edit recent image

	$page->open('Article', 'Edit homepage');

	$page->form_head();

	$page->hidden('action', 'saverecent');
	$query = "SELECT message_en,message_nl,color,article FROM messages LIMIT 0,1";
	$db->query($query);
	if ($db->next_row()) {
		$message_en = $db->row->message_en;
		$message_nl = $db->row->message_nl;
		$color = $db->row->color;
		$article = $db->row->article;
	} else {
		$message_en = '';
		$message_nl = '';
		$color = '009900';
		$article = 0;
	}

	$page->table_head(array('Homepage', 100, '', 0));
	$page->table_row(array('Message text Nederlands:', $page->input_memo('message_nl', htmlspecialchars($message_nl), 10)));
	$page->table_row(array('Message text Engels:', $page->input_memo('message_en', htmlspecialchars($message_en), 10)));
	$colors = '';
	foreach (array('009900', '00cc00', 'ff9900', '00ccff', 'ff33ff', 'ff3300', 'ff6600', 'ffcc00') as $aColor) {
		$colors .= "<input type=\"radio\" name=\"color\" value=\"$aColor\"";
		if ($aColor==$color) {
			$colors .= ' checked';
		}
		$colors .= "><div class=\"color\" style=\"background-color:#$aColor\">&nbsp;</div><br>";
	}
	$page->table_row(array('Message color:', $colors));
	$page->table_row(array('Link to article ID:', $page->input_text('article', $article, 5)));
	for ($i=1; $i<=NUM_RECENT_IMAGES; $i++) {
		$picfilename = PATH_PREFIX.IMAGE_PATH.sprintf(RECENT_IMAGE, $i);
		if (!file_exists($picfilename)) {
			$picfilename = false;
		}
		$del = '<input type="checkbox" name="delpic'.$i.'" value="1">delete image</input>';
		$input = ($picfilename ? "<img src=\"$picfilename\" height=\"100\" border=\"1\">&nbsp;$del<br>\nNew: " : "")."<input type=\"file\" name=\"pic$i\" accept=\"image/jpeg\" size=\"50\">".($picfilename ? "<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(fill only if you want to upload a new image)" : "");
		$page->table_row(array("Recent image #$i (JPEG):", $input));
	}
	$page->table_foot();

	$page->form_foot('recent');

	$page->close();
	exit;









elseif ($_REQUEST['action']=='saverecent'):

	// Save message
	$message_en = trim(htmlspecialchars_decode($_REQUEST['message_en']));
	$message_nl = trim(htmlspecialchars_decode($_REQUEST['message_nl']));
	$query = "DELETE FROM messages";
	$db->simplequery($query);
	$query = "INSERT INTO messages SET message_en='".mysql_real_escape_string($message_en)."',message_nl='".mysql_real_escape_string($message_nl)."',color='".mysql_real_escape_string($_REQUEST['color'])."',article='".intval($_REQUEST['article'])."'";
	$db->simplequery($query);

	define('MESSAGE_LINE_HEIGHT', HEADLINE_LINE_HEIGHT*2.5);

	// Create an image from the message
	$factor = 3;
	foreach (array('en'=>$message_en, 'nl'=>$message_nl) as $lang=>$message) {
		$fn = "../headlines/message_$lang.gif";
		@unlink($fn);

		$message = strtoupper(trim($message));
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
		$width = max($width, max($bbox[0],$bbox[2],$bbox[4],$bbox[6]) - min($bbox[0],$bbox[2],$bbox[4],$bbox[6]))+3;
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
					hexdec(substr($color, 0, 2)),
					hexdec(substr($color, 2, 2)),
					hexdec(substr($color, 4, 2))
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

	// Save recent image
	for ($i=1; $i<=NUM_RECENT_IMAGES; $i++) {
		$fn = PATH_PREFIX.IMAGE_PATH.sprintf(RECENT_IMAGE, $i);
		if ($_REQUEST["delpic$i"]==1) {
			// Delete picture
			@unlink($fn);
		} else {
			// Update picture
			if (($_FILES["pic$i"]['error']==0) && ($_FILES["pic$i"]['size']>0)) {
				// Delete old image
				@unlink($fn);
				// Copy new image
				move_uploaded_file($_FILES["pic$i"]['tmp_name'], $fn);
				chmod($fn, 0644);
			}
		}
	}

	// Back to list
	$page->redirect_url(SCRIPTNAME);
	exit;









else:

	// Default action: article overview

	$page->open('Article Overview','Article Overview');

?><script language="JavaScript" type="text/javascript">
function deleteEntry(id, caption) {
	if (confirm("Do you really want to delete the article \""+caption+"\" and all its sub-articles?\n\nAttention: You can NOT restore deleted articles!")
		&& confirm("Second and last confirmation: Do you really want to delete the article \""+caption+"\" an all its sub-articles?\n\nIf you click 'OK', the article \""+caption+"\" and all its sub-articles are ultimately deleted.\n\n")) {
		location.href= "<?php print SCRIPTNAME; ?>?id="+id+"&action=delete";
	}
}
</script>
<?php

	print '<p>'.$page->button(BUTTON_NEW, 'New article on the main level', SCRIPTNAME.'?action=edit&id='.ID_NEW.'&parent=0')
		.'&nbsp;&nbsp;<a href="'.SCRIPTNAME.'?action=recent">edit homepage</a>'
//		.'&nbsp;&nbsp;<a href="backup.php">Database backup</a>'
		.'</p>'."\n";

	$db2 = new database();

	function print_articles($level, $parent, $parentOrd, $parentNum, $db) {
		// Retrieve articles from the database
		$query = "SELECT id,title_nl AS title FROM articles WHERE parent=$parent ORDER BY ord";
		$db->query($query);
		$num = $db->num_rows();
		$i = 1;

		// Determine format code
		switch ($level) {
/*			case 0:
				$format = 'strong';
				break;
			case 1:
				$format = 'em';
				break;
*/			default:
				$format = 'span';
		}

		// Display article data
		while ($db->next_row()) {
			$expanded = in_array($db->row->id, $_SESSION['expanded']);
			$query = 'SELECT COUNT(id) AS num FROM articles WHERE parent='.$db->row->id;
			$GLOBALS['db2']->query($query);
			$GLOBALS['db2']->next_row();
			$expandable = $GLOBALS['db2']->row->num>0;
			$db->row->title = str_replace('\\', ' ', stripslashes($db->row->title));
			if ($db->row->title=='') {
				$db->row->title = '(no title specified)';
			}
			if ($i<$num) {
				$buttonDown = '<a href="'.SCRIPTNAME.'?action=down&id='.$db->row->id.'"><img src="down.gif" width="15" height="19" border="0" alt="down" align="middle" /></a>';
			} else if ($parentOrd<$parentNum) {
				$buttonDown = '<a href="'.SCRIPTNAME.'?action=catdown&id='.$db->row->id.'"><img src="catdown.gif" width="15" height="19" border="0" alt="category down" align="middle" /></a>';
			} else {
				$buttonDown = '<img src="down-off.gif" width="15" height="19" border="0" alt="" align="middle" />';
			}
			if ($i>1) {
				$buttonUp = '<a href="'.SCRIPTNAME.'?action=up&id='.$db->row->id.'"><img src="up.gif" width="15" height="19" border="0" alt="up" align="middle" /></a>';
			} else if ($parentOrd>1) {
				$buttonUp = '<a href="'.SCRIPTNAME.'?action=catup&id='.$db->row->id.'"><img src="catup.gif" width="15" height="19" border="0" alt="category up" align="middle" /></a>';
			} else {
				$buttonUp = '<img src="up-off.gif" width="15" height="19" border="0" alt="" align="middle" />';
			}
			$GLOBALS['page']->table_row(array(
				str_repeat('<img src="blank.gif" width="33" height="19" border="0" alt="" align="middle" />&nbsp;&nbsp;', $level)
				.($expandable ? '<a href="'.SCRIPTNAME.'?action=expand&id='.$db->row->id.'"><img src="'.($expanded ? 'collapse' : 'expand').'.gif" width="15" height="19" border="0" alt="" align="middle" /></a>' : '<img src="blank.gif" width="15" height="19" border="0" alt="" align="middle" />')
				.'<img src="blank.gif" width="10" height="19" border="0" alt="" align="middle" />'
				.$buttonDown
				.'<img src="blank.gif" width="3" height="19" border="0" alt="" align="middle" />'
				.$buttonUp
				.'<a name="'.$db->row->id.'">&nbsp;&nbsp;</a>'
				."<$format>".'<a href="'.SCRIPTNAME.'?action=edit&id='.$db->row->id.'">'.$db->row->title."</a></$format>",
				'<a href="javascript:deleteEntry(\''.$db->row->id.'\',\''.str_replace(array('*', '"', "'"), '', $db->row->title).'\');">delete</a>'
				.($level<MAXIMUM_ARTICLE_LEVEL ? '&nbsp;|&nbsp;create sub-article as <a href="'.SCRIPTNAME.'?action=edit&id='.ID_NEW.'&parent='.$db->row->id.'&order=1">first</a> / <a href="'.SCRIPTNAME.'?action=edit&id='.ID_NEW.'&parent='.$db->row->id.'">last</a> child' : '')
			));
			if ($expanded) {
				// Display child articles
				print_articles($level+1, $db->row->id, $i, $num, new database());
			}
			$i++;
		}
	}

	$page->table_head(array("Articles", 500, "", 0));
	print_articles(0, 0, 0, 0, $db);
	$page->table_foot();

	$page->close();



endif;

?>