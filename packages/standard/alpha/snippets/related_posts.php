<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

		<@ newPagelist { type: 'related', sort: 'date desc' } @>
		<@ if @{ :pagelistCount } @>
			<div class="uk-block">
				<h2 class="uk-margin-top">@{ labelRelated | def ('Related') }</h2>
				<@ pagelist_blog.php @>
			</div>
		<@ end @>
	