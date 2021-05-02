<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ if @{ date } or @{ tags } ~@>
	<div class="text-subtitle">
		<@ ../elements/date.php @>
		<@ if @{ date } and @{ tags } @>&nbsp;&mdash;&nbsp;<br class="uk-visible-small"><@ end @>
		<@ ../elements/tags.php @>
	</div>
<@~ end @>