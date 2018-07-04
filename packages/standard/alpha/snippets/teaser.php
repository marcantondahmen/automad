<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

	<@ if @{ textTeaser } or not @{ checkboxHideTitle } @>
		<div class="uk-block">
			<ul class="uk-grid grid-margin">
				<@ if not @{ checkboxHideTitle } @>
					<li class="uk-width-medium-1-3">
						<h1>@{ title }</h1>
					</li>
				<@ end @>
				<@ if @{ textTeaser } @>
					<li class="content uk-width-medium-2-3">
						@{ textTeaser | markdown }
					</li>	
				<@ end @>
			</ul>
		</div>
	<@ end @>
	