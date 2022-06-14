{include "head.tpl"}
	<div id="content">
		<div id="text">
			{$article->text|level:3}
		</div>

		<div id="beeld">
			<hr />
{foreach name=images from=$article->images item=aImageRow}

			<div class="image-row">
{foreach from=$aImageRow item=aImage}
{if $aImage->youtubeid}
				<iframe class="image-container" data-width="{$aImage->size.0}" data-height="{$aImage->size.1}" width="{$aImage->size.0}" height="{$aImage->size.1}" src="//www.youtube.com/embed/{$aImage->youtubeid}?rel=0" frameborder="0" allowfullscreen></iframe>

{else}
{if substr($aImage->image, -3) != "png"}
				<img class="image-container" src="{$documentroot}{$aImage->image}?w=960" data-width="{$aImage->size.0}" data-height="{$aImage->size.1}" alt="">
{else}
				<img class="image-container" src="{$documentroot}{$aImage->image}" data-width="{$aImage->size.0}" data-height="{$aImage->size.1}" alt="">
{/if}
{/if}
{/foreach}
			</div>
{/foreach}
		</div>
	</div>
{include "foot.tpl"}
