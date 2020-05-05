<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ if @{ date } or @{ tags } ~@>
	<div class="text-subtitle">
		<@ ../../snippets/date.php @>
		<@ if @{ date } and @{ tags } @>&nbsp;&mdash;&nbsp;<br class="uk-visible-small"><@ end @>
		<@ tags.php @>
	</div>
<@~ end @>