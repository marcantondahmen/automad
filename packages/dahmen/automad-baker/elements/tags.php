<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

	<@ if @{ tags } @>
		<div class="baker-buttons-stacked">
			<@ foreach in tags 
				@><a 
				href="@{ urlSearchResults | def('/') }?filter=@{ :tag }" 
				class="uk-button uk-button-primary uk-button-mini"
				>
					#@{ :tag }
				</a><@ 
			end @>
		</div>	
	<@ end @>
	