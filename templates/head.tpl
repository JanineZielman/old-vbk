<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$meta->language}" lang="{$meta->language}">
<head>
{metadata meta=$meta}
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<script type="text/javascript" src="{$documentroot}scripts/jquery-1.11.1.min.js"></script>
	<script type="text/javascript" src="{$documentroot}scripts/main.js?1"></script>
	<link rel="stylesheet" href="http://webfonts.fontslive.com/css/cff9f73a-c56b-4a5e-a21a-6f0673d9092d.css" type="text/css" />
	<link rel="stylesheet" type="text/css" media="all" href="{$documentroot}styles/vbk.css?2" />
	<script type="text/javascript">
		{literal}var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-3748362-1']);
		_gaq.push(['_trackPageview']);
		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();{/literal}
	</script>
	<!--
		Design by Catalogtree (http://www.catalogtree.net/)
		Realization by Systemantics (http://www.systemantics.net/)
	-->
</head>
<body>
	<div class="header">
		<span class="zwart"><a href="{$documentroot}{$lang}/"><img class="logo" src="/elements/logo_vBK_outline.svg" alt="van Bergen Kolpa Architecten" /></a></span>
		<div id="nav">
			<div id="navkop1">
				<a href="{$documentroot}{if $lang=='en'}nl/{if $article}{$article->_id}_{$article->title_nl|sluggize}.html{/if}{else}en/{if $article}{$article->_id}_{$article->title_en|sluggize}.html{/if}{/if}">{if $lang=='en'}NL{else}ENG{/if}</a> /
{foreach name=sections from=$sections item=otherSection}
				<a href="{$documentroot}{$lang}/{$otherSection->_id}_{$otherSection->title|sluggize}.html"{if $section->_id==$otherSection->_id} class="hier"{/if}>{$otherSection->title|trim}</a>{if !$smarty.foreach.sections.last} /{/if}

{/foreach}
			</div>
{if $articles}
			<div id="navkop2">
	{foreach name=articles from=$articles item=otherArticle}
				<a href="{$documentroot}{$lang}/{$otherArticle->_id}_{$otherArticle->title|sluggize}.html"{if $article->_id==$otherArticle->_id} class="hier"{/if}>{$otherArticle->title}</a>{if !$smarty.foreach.articles.last} /{/if}
	{/foreach}
			</div>
{/if}
	    </div>
	</div>
