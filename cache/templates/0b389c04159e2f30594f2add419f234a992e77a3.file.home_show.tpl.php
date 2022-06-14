<?php /* Smarty version Smarty-3.1.5, created on 2014-11-07 10:23:01
         compiled from "templates/home_show.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1161831600545b4a9e16f745-04403891%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0b389c04159e2f30594f2add419f234a992e77a3' => 
    array (
      0 => 'templates/home_show.tpl',
      1 => 1415352181,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1161831600545b4a9e16f745-04403891',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.5',
  'unifunc' => 'content_545b4a9e21024',
  'variables' => 
  array (
    'message' => 0,
    'documentroot' => 0,
    'lang' => 0,
    'recentimage' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_545b4a9e21024')) {function content_545b4a9e21024($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_level')) include '/sites/vanbergenkolpa.nl/www/libs/plugins/modifier.level.php';
?><?php echo $_smarty_tpl->getSubTemplate ("head.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div id="content">
<div id="text"><hr></div>
<div id="beeld">
<hr />
</div>
</div>
	<div id="recent">
<?php if ($_smarty_tpl->tpl_vars['message']->value){?>
		<div class="message">
			<div class="big" style="color:#<?php echo $_smarty_tpl->tpl_vars['message']->value->color;?>
;">
				<?php echo smarty_modifier_level(nl2br(trim($_smarty_tpl->tpl_vars['message']->value->text)),4);?>

			</div>
	<?php if ($_smarty_tpl->tpl_vars['message']->value->article!=-1){?>
			<br /><br /><a style="background-color:#<?php echo $_smarty_tpl->tpl_vars['message']->value->color;?>
;" href="<?php echo $_smarty_tpl->tpl_vars['documentroot']->value;?>
<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['message']->value->article;?>
.html"><?php echo $_smarty_tpl->getConfigVariable('readmore');?>
 &gt;&gt;&gt;</a>
	<?php }?>
		</div>
<?php }?>
		<img src="<?php echo $_smarty_tpl->tpl_vars['documentroot']->value;?>
<?php echo $_smarty_tpl->tpl_vars['recentimage']->value->image;?>
" width="100%" height="80%" alt="" />
	</div>
<?php echo $_smarty_tpl->getSubTemplate ("foot.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php }} ?>