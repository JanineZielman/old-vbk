{extends file="main.tpl"}
{block name="content"}
	<div id="welcome" class="block">
		<h2>{$text->title|htmlspecialchars}</h2>
		{$text->text|level:2}
	</div>
{/block}
