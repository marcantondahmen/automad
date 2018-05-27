<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

	<@ if @{ textTeaser } or not @{ checkboxHideTitle } @>
		<div class="uk-block">
			<ul class="uk-grid grid-margin uk-grid-width-medium-1-2">
				<@ if not @{ checkboxHideTitle } @>
					<li>
						<h1>@{ title }</h1>
					</li>
				<@ end @>
				<@ if @{ textTeaser } @>
					<li class="content">
						@{ textTeaser | markdown }
					</li>	
				<@ end @>
			</ul>
		</div>
	<@ end @>
	