{include "head.tpl"}
<div id="content">
<div id="text"><hr></div>
<div id="beeld">
<hr />
</div>
</div>
	<div id="recent">
{if $message}
		<div class="message">
			<div class="big" style="color:#{$message->color};">
				{$message->text|trim|nl2br|level:4}
			</div>
	{if $message->article!=-1}
			<br /><br /><a style="background-color:#{$message->color};" href="{$documentroot}{$lang}/{$message->article}.html">{#readmore#} &gt;&gt;&gt;</a>
	{/if}
		</div>
{/if}
		<img src="{$documentroot}{$recentimage->image}" width="100%" height="80%" alt="" />
	</div>
{include "foot.tpl"}
