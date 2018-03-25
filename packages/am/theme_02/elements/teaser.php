<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

	<@ if not @{ checkboxHideTeaser } @>
		<h1>@{ title }</h1>
		<@ if @{ textTeaser } @>
			<div class="am-02-content uk-text-large uk-margin-top">
				@{ textTeaser | markdown }
			</div>	
		<@ end @>	
	<@ end @>