<?php /* Smarty version Smarty-3.1.5, created on 2012-04-26 16:01:13
         compiled from "templates/home_show.tpl" */ ?>
<?php /*%%SmartyHeaderCode:4129212864f734ae08980b7-57472429%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0b389c04159e2f30594f2add419f234a992e77a3' => 
    array (
      0 => 'templates/home_show.tpl',
      1 => 1333559666,
      2 => 'file',
    ),
    '88c6baab8db5b147146df4d4d7f083fab98802ca' => 
    array (
      0 => 'templates/main.tpl',
      1 => 1335448862,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4129212864f734ae08980b7-57472429',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.5',
  'unifunc' => 'content_4f734ae0a83fd',
  'variables' => 
  array (
    'meta' => 0,
    'documentroot' => 0,
    'backgroundimage' => 0,
    'homeUrl' => 0,
    'persons' => 0,
    'aPerson' => 0,
    'questions' => 0,
    'aQuestion' => 0,
    'news' => 0,
    'aNews' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4f734ae0a83fd')) {function content_4f734ae0a83fd($_smarty_tpl) {?><?php if (!is_callable('smarty_function_metadata')) include '/sites/vanbergenkolpa.nl/www/parksupermarkt/libs/plugins/function.metadata.php';
if (!is_callable('smarty_modifier_level')) include '/sites/vanbergenkolpa.nl/www/parksupermarkt/libs/plugins/modifier.level.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $_smarty_tpl->tpl_vars['meta']->value->language;?>
" lang="<?php echo $_smarty_tpl->tpl_vars['meta']->value->language;?>
">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<?php echo smarty_function_metadata(array('meta'=>$_smarty_tpl->tpl_vars['meta']->value),$_smarty_tpl);?>

	<link rel="stylesheet" type="text/css" media="all" href="<?php echo $_smarty_tpl->tpl_vars['documentroot']->value;?>
styles/main.css" />
	<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['documentroot']->value;?>
scripts/jquery-1.7.min.js"></script>
	<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['documentroot']->value;?>
scripts/jquery.isotope.min.js"></script>
	<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['documentroot']->value;?>
scripts/jquery.webticker.js"></script>
	<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['documentroot']->value;?>
scripts/jquery.backstretch.min.js"></script>
	<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['documentroot']->value;?>
scripts/main.js"></script>
	<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-3748362-1']);
		_gaq.push(['_trackPageview']);
		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
	</script>
	<!--
		Design by Catalogtree (http://www.catalogtree.net/)
		Technical realization by Systemantics (http://www.systemantics.net/)
	-->
</head>
<body style="background-image:url(<?php echo $_smarty_tpl->tpl_vars['documentroot']->value;?>
<?php echo $_smarty_tpl->tpl_vars['backgroundimage']->value->image;?>
);">
	<div id="header">
		<h1><a href="<?php echo $_smarty_tpl->tpl_vars['homeUrl']->value;?>
">Park Supermarkt</a></h1>
		<div id="menus">
			<div id="menu_persons" class="menu">
				<div class="label">
					Kies een persoon &raquo;
				</div>
				<ul>
<?php  $_smarty_tpl->tpl_vars['aPerson'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['aPerson']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['persons']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['aPerson']->key => $_smarty_tpl->tpl_vars['aPerson']->value){
$_smarty_tpl->tpl_vars['aPerson']->_loop = true;
?>
					<li><a href="<?php echo $_smarty_tpl->tpl_vars['documentroot']->value;?>
personen/<?php echo $_smarty_tpl->tpl_vars['aPerson']->value->_slug;?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['aPerson']->value->name);?>
</a></li>
<?php } ?>
				</ul>
			</div>
			<div id="menu_questions" class="menu">
				<div class="label">
					Kies een vraag &raquo;
				</div>
				<ul>
<?php  $_smarty_tpl->tpl_vars['aQuestion'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['aQuestion']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['questions']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['aQuestion']->key => $_smarty_tpl->tpl_vars['aQuestion']->value){
$_smarty_tpl->tpl_vars['aQuestion']->_loop = true;
?>
					<li><a href="<?php echo $_smarty_tpl->tpl_vars['documentroot']->value;?>
vragen/<?php echo $_smarty_tpl->tpl_vars['aQuestion']->value->_slug;?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['aQuestion']->value->shortquestion);?>
</a></li>
<?php } ?>
				</ul>
			</div>
		</div>
	</div>
	<div id="answers">

	<div id="welcome" class="block">
		<h2><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['text']->value->title);?>
</h2>
		<?php echo smarty_modifier_level($_smarty_tpl->tpl_vars['text']->value->text,2);?>

	</div>

	</div>
	<div id="footer">
		<ul id="ticker">
<?php  $_smarty_tpl->tpl_vars['aNews'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['aNews']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['news']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['aNews']->key => $_smarty_tpl->tpl_vars['aNews']->value){
$_smarty_tpl->tpl_vars['aNews']->_loop = true;
?>
			<li style="color:#<?php echo $_smarty_tpl->tpl_vars['aNews']->value->color;?>
;"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['aNews']->value->text);?>
 +++&nbsp;</li>
<?php } ?>
		</ul>
	</div>
</body>
</html>
<?php }} ?>