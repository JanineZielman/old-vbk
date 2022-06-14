<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$meta->language}" lang="{$meta->language}">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
{metadata meta=$meta}
	<link rel="stylesheet" type="text/css" media="all" href="{$documentroot}styles/main.css" />
	<script type="text/javascript" src="{$documentroot}scripts/jquery-1.7.min.js"></script>
	<script type="text/javascript" src="{$documentroot}scripts/jquery.isotope.min.js"></script>
	<script type="text/javascript" src="{$documentroot}scripts/jquery.webticker.js"></script>
	<script type="text/javascript" src="{$documentroot}scripts/jquery.backstretch.min.js"></script>
	<script type="text/javascript" src="{$documentroot}scripts/main.js"></script>
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
		Technical realization by Systemantics (http://www.systemantics.net/)
	-->
</head>
<body style="background-image:url({$documentroot}{$backgroundimage->image});">
	<div id="header">
		<h1><a href="{$homeUrl}">Park Supermarkt</a></h1>
		<div id="menus">
			<div id="menu_persons" class="menu">
				<div class="label">
					Kies een persoon &raquo;
				</div>
				<ul>
{foreach $persons as $aPerson}
					<li><a href="{$documentroot}personen/{$aPerson->_slug}">{$aPerson->name|htmlspecialchars}</a></li>
{/foreach}
				</ul>
			</div>
			<div id="menu_questions" class="menu">
				<div class="label">
					Kies een vraag &raquo;
				</div>
				<ul>
{foreach $questions as $aQuestion}
					<li><a href="{$documentroot}vragen/{$aQuestion->_slug}">{$aQuestion->shortquestion|htmlspecialchars}</a></li>
{/foreach}
				</ul>
			</div>
		</div>
	</div>
	<div id="answers">
{block name="content"}{/block}
	</div>
	<div id="footer">
		<ul id="ticker">
{foreach $news as $aNews}
			<li style="color:#{$aNews->color};">{$aNews->text|htmlspecialchars} +++&nbsp;</li>
{/foreach}
		</ul>
	</div>
</body>
</html>
