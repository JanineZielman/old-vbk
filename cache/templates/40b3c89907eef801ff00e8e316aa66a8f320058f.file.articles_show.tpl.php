<?php /* Smarty version Smarty-3.1.5, created on 2014-11-06 11:36:39
         compiled from "templates/articles_show.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1519816215545b4a8d2a3305-21347568%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '40b3c89907eef801ff00e8e316aa66a8f320058f' => 
    array (
      0 => 'templates/articles_show.tpl',
      1 => 1415270195,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1519816215545b4a8d2a3305-21347568',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.5',
  'unifunc' => 'content_545b4a8d3bdd6',
  'variables' => 
  array (
    'article' => 0,
    'aImageRow' => 0,
    'aImage' => 0,
    'documentroot' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_545b4a8d3bdd6')) {function content_545b4a8d3bdd6($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_level')) include '/sites/vanbergenkolpa.nl/www/libs/plugins/modifier.level.php';
?><?php echo $_smarty_tpl->getSubTemplate ("head.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

	<div id="content">
		<div id="text">
			<?php echo smarty_modifier_level($_smarty_tpl->tpl_vars['article']->value->text,3);?>

		</div>

		<div id="beeld">
			<hr />
<?php  $_smarty_tpl->tpl_vars['aImageRow'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['aImageRow']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['article']->value->images; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['aImageRow']->key => $_smarty_tpl->tpl_vars['aImageRow']->value){
$_smarty_tpl->tpl_vars['aImageRow']->_loop = true;
?>

			<div class="image-row">
<?php  $_smarty_tpl->tpl_vars['aImage'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['aImage']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['aImageRow']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['aImage']->key => $_smarty_tpl->tpl_vars['aImage']->value){
$_smarty_tpl->tpl_vars['aImage']->_loop = true;
?>
<?php if ($_smarty_tpl->tpl_vars['aImage']->value->youtubeid){?>
				<iframe class="image-container" data-width="<?php echo $_smarty_tpl->tpl_vars['aImage']->value->size[0];?>
" data-height="<?php echo $_smarty_tpl->tpl_vars['aImage']->value->size[1];?>
" width="<?php echo $_smarty_tpl->tpl_vars['aImage']->value->size[0];?>
" height="<?php echo $_smarty_tpl->tpl_vars['aImage']->value->size[1];?>
" src="//www.youtube.com/embed/<?php echo $_smarty_tpl->tpl_vars['aImage']->value->youtubeid;?>
?rel=0" frameborder="0" allowfullscreen></iframe>

<?php }else{ ?>
<?php if (substr($_smarty_tpl->tpl_vars['aImage']->value->image,-3)!="png"){?>
				<img class="image-container" src="<?php echo $_smarty_tpl->tpl_vars['documentroot']->value;?>
<?php echo $_smarty_tpl->tpl_vars['aImage']->value->image;?>
?w=960" data-width="<?php echo $_smarty_tpl->tpl_vars['aImage']->value->size[0];?>
" data-height="<?php echo $_smarty_tpl->tpl_vars['aImage']->value->size[1];?>
" alt="">
<?php }else{ ?>
				<img class="image-container" src="<?php echo $_smarty_tpl->tpl_vars['documentroot']->value;?>
<?php echo $_smarty_tpl->tpl_vars['aImage']->value->image;?>
" data-width="<?php echo $_smarty_tpl->tpl_vars['aImage']->value->size[0];?>
" data-height="<?php echo $_smarty_tpl->tpl_vars['aImage']->value->size[1];?>
" alt="">
<?php }?>
<?php }?>
<?php } ?>
			</div>
<?php } ?>
		</div>
	</div>
<?php echo $_smarty_tpl->getSubTemplate ("foot.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php }} ?>