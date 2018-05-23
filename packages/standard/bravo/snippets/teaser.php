<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

	<@ if @{ textTeaser } or not @{ checkboxHideTitle } @>
		<ul class="uk-grid uk-grid-width-medium-1-2">
			<@ if not @{ checkboxHideTitle } @>
				<li class="uk-block">
					<h1>@{ title }</h1>
				</li>
			<@ end @>
			<@ if @{ textTeaser } @>
				<li class="content uk-block">
					@{ textTeaser | markdown }
				</li>	
			<@ end @>
		</ul>
	<@ end @>
	