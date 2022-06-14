		<div class="person cf{if $block} block{/if}">
			{html_image file="{$documentroot}{$person->imageThumb}" alt=""}
			<div class="details">
				<strong>{$person->name|htmlspecialchars}</strong><br />
				{$person->role|htmlspecialchars}
			</div>
		</div>
