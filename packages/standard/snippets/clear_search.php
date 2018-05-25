<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

	<@ if @{ ?search } @>
		<a href="?<@ queryStringMerge { search: false } @>" class="uk-button">
			<i class="uk-icon-remove"></i>&nbsp;
			@{ labelClearSearch | def('Search Results for') } "@{ ?search }"
		</a>
	<@ end @>