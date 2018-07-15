<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>	
	<# Show full date for posts and skip days for all other templates. #>
	<@ if @{ :template | match('/post/') } @>
		@{ date | dateFormat (@{ formatDatePost | def ('l, F jS Y')}) }
	<@ else @>
		@{ date | dateFormat (@{ formatDateProject | def ('F Y') }) }
	<@ end @>