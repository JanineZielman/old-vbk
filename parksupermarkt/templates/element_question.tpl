		<div class="question{if $block} block" style="box-shadow:2px 2px 0px #{$question->color};-moz-box-shadow:2px 2px 0px #{$question->color};-webkit-box-shadow:2px 2px 0px #{$question->color};{/if}">
			<div class="number">{$question->_order|string_format:"%02d"}</div>
			<h2>{$question->longquestion|htmlspecialchars}</h2>
		</div>
