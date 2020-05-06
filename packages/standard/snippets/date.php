<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>	
	<# Show full date for posts and skip days for all other templates. #>
	<@~ if @{ :template | match('/post/') } or @{ url | match('/blog\\//') } ~@>
		@{ date | dateFormat (@{ formatDatePost | def ('l, F jS Y')}, @{ locale }) }
	<@~ else ~@>
		@{ date | dateFormat (@{ formatDateProject | def ('F Y') }, @{ locale }) }
	<@~ end ~@>