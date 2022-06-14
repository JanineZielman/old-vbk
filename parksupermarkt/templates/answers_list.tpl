{extends file="main.tpl"}
{block name="content"}
{if $person}{include file="element_person.tpl" block=true}{/if}
{if $question}{include file="element_question.tpl" block=true}{/if}
{foreach $answers as $aAnswer}
		<div class="answer block" style="box-shadow:2px 2px 0px #{$aAnswer->question->color};-moz-box-shadow:2px 2px 0px #{$aAnswer->question->color};-webkit-box-shadow:2px 2px 0px #{$aAnswer->question->color};">
{if $person}
{include file="element_question.tpl" question=$aAnswer->question}
{/if}
{if $question}
{include file="element_person.tpl" person=$aAnswer->person}
{/if}
			{$aAnswer->answer|level:2}
{if $aAnswer->image}
			{html_image file="{$documentroot}{$aAnswer->imageThumb}" alt=""}
{/if}
		</div>
{/foreach}	
{/block}
