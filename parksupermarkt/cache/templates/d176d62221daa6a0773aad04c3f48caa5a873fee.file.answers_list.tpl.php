<?php /* Smarty version Smarty-3.1.5, created on 2012-04-26 17:39:56
         compiled from "templates/answers_list.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15513726434f734b173255b5-22860972%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd176d62221daa6a0773aad04c3f48caa5a873fee' => 
    array (
      0 => 'templates/answers_list.tpl',
      1 => 1333560261,
      2 => 'file',
    ),
    '88c6baab8db5b147146df4d4d7f083fab98802ca' => 
    array (
      0 => 'templates/main.tpl',
      1 => 1335448862,
      2 => 'file',
    ),
    '4643f14552063c4477c2a9d235218d9fe95985ab' => 
    array (
      0 => 'templates/element_person.tpl',
      1 => 1333567569,
      2 => 'file',
    ),
    '64df745b6ab8b9ad909feb3ed196cc5d2afdfc6e' => 
    array (
      0 => 'templates/element_question.tpl',
      1 => 1333559666,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15513726434f734b173255b5-22860972',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.5',
  'unifunc' => 'content_4f734b176414d',
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
<?php if ($_valid && !is_callable('content_4f734b176414d')) {function content_4f734b176414d($_smarty_tpl) {?><?php if (!is_callable('smarty_function_metadata')) include '/sites/vanbergenkolpa.nl/www/parksupermarkt/libs/plugins/function.metadata.php';
if (!is_callable('smarty_modifier_level')) include '/sites/vanbergenkolpa.nl/www/parksupermarkt/libs/plugins/modifier.level.php';
if (!is_callable('smarty_function_html_image')) include '/sites/vanbergenkolpa.nl/www/parksupermarkt/libs/plugins/function.html_image.php';
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

<?php if ($_smarty_tpl->tpl_vars['person']->value){?><?php /*  Call merged included template "element_person.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate("element_person.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('block'=>true), 0, '15513726434f734b173255b5-22860972');
content_4f996c4cca990($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); /*  End of included template "element_person.tpl" */?><?php }?>
<?php if ($_smarty_tpl->tpl_vars['question']->value){?><?php /*  Call merged included template "element_question.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate("element_question.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('block'=>true), 0, '15513726434f734b173255b5-22860972');
content_4f996c4cd6125($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); /*  End of included template "element_question.tpl" */?><?php }?>
<?php  $_smarty_tpl->tpl_vars['aAnswer'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['aAnswer']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['answers']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['aAnswer']->key => $_smarty_tpl->tpl_vars['aAnswer']->value){
$_smarty_tpl->tpl_vars['aAnswer']->_loop = true;
?>
		<div class="answer block" style="box-shadow:2px 2px 0px #<?php echo $_smarty_tpl->tpl_vars['aAnswer']->value->question->color;?>
;-moz-box-shadow:2px 2px 0px #<?php echo $_smarty_tpl->tpl_vars['aAnswer']->value->question->color;?>
;-webkit-box-shadow:2px 2px 0px #<?php echo $_smarty_tpl->tpl_vars['aAnswer']->value->question->color;?>
;">
<?php if ($_smarty_tpl->tpl_vars['person']->value){?>
<?php /*  Call merged included template "element_question.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate("element_question.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('question'=>$_smarty_tpl->tpl_vars['aAnswer']->value->question), 0, '15513726434f734b173255b5-22860972');
content_4f996c4cd6125($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); /*  End of included template "element_question.tpl" */?>
<?php }?>
<?php if ($_smarty_tpl->tpl_vars['question']->value){?>
<?php /*  Call merged included template "element_person.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate("element_person.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('person'=>$_smarty_tpl->tpl_vars['aAnswer']->value->person), 0, '15513726434f734b173255b5-22860972');
content_4f996c4cca990($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); /*  End of included template "element_person.tpl" */?>
<?php }?>
			<?php echo smarty_modifier_level($_smarty_tpl->tpl_vars['aAnswer']->value->answer,2);?>

<?php if ($_smarty_tpl->tpl_vars['aAnswer']->value->image){?>
			<?php echo smarty_function_html_image(array('file'=>($_smarty_tpl->tpl_vars['documentroot']->value).($_smarty_tpl->tpl_vars['aAnswer']->value->imageThumb),'alt'=>''),$_smarty_tpl);?>

<?php }?>
		</div>
<?php } ?>	

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
<?php }} ?><?php /* Smarty version Smarty-3.1.5, created on 2012-04-26 17:39:56
         compiled from "templates/element_person.tpl" */ ?>
<?php if ($_valid && !is_callable('content_4f996c4cca990')) {function content_4f996c4cca990($_smarty_tpl) {?><?php if (!is_callable('smarty_function_html_image')) include '/sites/vanbergenkolpa.nl/www/parksupermarkt/libs/plugins/function.html_image.php';
?>		<div class="person cf<?php if ($_smarty_tpl->tpl_vars['block']->value){?> block<?php }?>">
			<?php echo smarty_function_html_image(array('file'=>($_smarty_tpl->tpl_vars['documentroot']->value).($_smarty_tpl->tpl_vars['person']->value->imageThumb),'alt'=>''),$_smarty_tpl);?>

			<div class="details">
				<strong><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['person']->value->name);?>
</strong><br />
				<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['person']->value->role);?>

			</div>
		</div>
<?php }} ?><?php /* Smarty version Smarty-3.1.5, created on 2012-04-26 17:39:56
         compiled from "templates/element_question.tpl" */ ?>
<?php if ($_valid && !is_callable('content_4f996c4cd6125')) {function content_4f996c4cd6125($_smarty_tpl) {?>		<div class="question<?php if ($_smarty_tpl->tpl_vars['block']->value){?> block" style="box-shadow:2px 2px 0px #<?php echo $_smarty_tpl->tpl_vars['question']->value->color;?>
;-moz-box-shadow:2px 2px 0px #<?php echo $_smarty_tpl->tpl_vars['question']->value->color;?>
;-webkit-box-shadow:2px 2px 0px #<?php echo $_smarty_tpl->tpl_vars['question']->value->color;?>
;<?php }?>">
			<div class="number"><?php echo sprintf("%02d",$_smarty_tpl->tpl_vars['question']->value->_order);?>
</div>
			<h2><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['question']->value->longquestion);?>
</h2>
		</div>
<?php }} ?>