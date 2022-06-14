<?php /* Smarty version Smarty-3.1.5, created on 2021-01-21 22:38:36
         compiled from "templates/head.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1734115970545b4a8d42ee02-32012304%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7219aedfef46ca98be08e95f488def87265787a5' => 
    array (
      0 => 'templates/head.tpl',
      1 => 1611265114,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1734115970545b4a8d42ee02-32012304',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.5',
  'unifunc' => 'content_545b4a8d51d9f',
  'variables' => 
  array (
    'meta' => 0,
    'documentroot' => 0,
    'lang' => 0,
    'article' => 0,
    'sections' => 0,
    'otherSection' => 0,
    'section' => 0,
    'articles' => 0,
    'otherArticle' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_545b4a8d51d9f')) {function content_545b4a8d51d9f($_smarty_tpl) {?><?php if (!is_callable('smarty_function_metadata')) include '/www/libs/plugins/function.metadata.php';
if (!is_callable('smarty_modifier_sluggize')) include '/www/libs/plugins/modifier.sluggize.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $_smarty_tpl->tpl_vars['meta']->value->language;?>
" lang="<?php echo $_smarty_tpl->tpl_vars['meta']->value->language;?>
">
<head>
<?php echo smarty_function_metadata(array('meta'=>$_smarty_tpl->tpl_vars['meta']->value),$_smarty_tpl);?>

	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['documentroot']->value;?>
scripts/jquery-1.11.1.min.js"></script>
	<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['documentroot']->value;?>
scripts/main.js?1"></script>
	<link rel="stylesheet" href="http://webfonts.fontslive.com/css/cff9f73a-c56b-4a5e-a21a-6f0673d9092d.css" type="text/css" />
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo $_smarty_tpl->tpl_vars['documentroot']->value;?>
styles/vbk.css?2" />
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
		Realization by Systemantics (http://www.systemantics.net/)
	-->
</head>
<body>
	<div class="header">
		<span class="zwart"><a href="<?php echo $_smarty_tpl->tpl_vars['documentroot']->value;?>
<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
/"><img class="logo" src="/elements/logo_vBK_outline.svg" alt="van Bergen Kolpa Architecten" /></a></span>
		<div id="nav">
			<div id="navkop1">
				<a href="<?php echo $_smarty_tpl->tpl_vars['documentroot']->value;?>
<?php if ($_smarty_tpl->tpl_vars['lang']->value=='en'){?>nl/<?php if ($_smarty_tpl->tpl_vars['article']->value){?><?php echo $_smarty_tpl->tpl_vars['article']->value->_id;?>
_<?php echo smarty_modifier_sluggize($_smarty_tpl->tpl_vars['article']->value->title_nl);?>
.html<?php }?><?php }else{ ?>en/<?php if ($_smarty_tpl->tpl_vars['article']->value){?><?php echo $_smarty_tpl->tpl_vars['article']->value->_id;?>
_<?php echo smarty_modifier_sluggize($_smarty_tpl->tpl_vars['article']->value->title_en);?>
.html<?php }?><?php }?>"><?php if ($_smarty_tpl->tpl_vars['lang']->value=='en'){?>NL<?php }else{ ?>ENG<?php }?></a> /
<?php  $_smarty_tpl->tpl_vars['otherSection'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['otherSection']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['sections']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['otherSection']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['otherSection']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['otherSection']->key => $_smarty_tpl->tpl_vars['otherSection']->value){
$_smarty_tpl->tpl_vars['otherSection']->_loop = true;
 $_smarty_tpl->tpl_vars['otherSection']->iteration++;
 $_smarty_tpl->tpl_vars['otherSection']->last = $_smarty_tpl->tpl_vars['otherSection']->iteration === $_smarty_tpl->tpl_vars['otherSection']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['sections']['last'] = $_smarty_tpl->tpl_vars['otherSection']->last;
?>
				<a href="<?php echo $_smarty_tpl->tpl_vars['documentroot']->value;?>
<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['otherSection']->value->_id;?>
_<?php echo smarty_modifier_sluggize($_smarty_tpl->tpl_vars['otherSection']->value->title);?>
.html"<?php if ($_smarty_tpl->tpl_vars['section']->value->_id==$_smarty_tpl->tpl_vars['otherSection']->value->_id){?> class="hier"<?php }?>><?php echo trim($_smarty_tpl->tpl_vars['otherSection']->value->title);?>
</a><?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['sections']['last']){?> /<?php }?>

<?php } ?>
			</div>
<?php if ($_smarty_tpl->tpl_vars['articles']->value){?>
			<div id="navkop2">
	<?php  $_smarty_tpl->tpl_vars['otherArticle'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['otherArticle']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['articles']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['otherArticle']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['otherArticle']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['otherArticle']->key => $_smarty_tpl->tpl_vars['otherArticle']->value){
$_smarty_tpl->tpl_vars['otherArticle']->_loop = true;
 $_smarty_tpl->tpl_vars['otherArticle']->iteration++;
 $_smarty_tpl->tpl_vars['otherArticle']->last = $_smarty_tpl->tpl_vars['otherArticle']->iteration === $_smarty_tpl->tpl_vars['otherArticle']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['articles']['last'] = $_smarty_tpl->tpl_vars['otherArticle']->last;
?>
				<a href="<?php echo $_smarty_tpl->tpl_vars['documentroot']->value;?>
<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['otherArticle']->value->_id;?>
_<?php echo smarty_modifier_sluggize($_smarty_tpl->tpl_vars['otherArticle']->value->title);?>
.html"<?php if ($_smarty_tpl->tpl_vars['article']->value->_id==$_smarty_tpl->tpl_vars['otherArticle']->value->_id){?> class="hier"<?php }?>><?php echo $_smarty_tpl->tpl_vars['otherArticle']->value->title;?>
</a><?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['articles']['last']){?> /<?php }?>
	<?php } ?>
			</div>
<?php }?>
	    </div>
	</div>
<?php }} ?>