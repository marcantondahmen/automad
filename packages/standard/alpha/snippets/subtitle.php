<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ if @{ date } or @{ tags } ~@>
	<div class="text-subtitle">
		<@ ../../snippets/date.php @>
		<@ if @{ date } and @{ tags } @>&nbsp;&mdash;&nbsp;<@ end @>
		<br class="uk-visible-small">
		<@ ../../snippets/tags.php @>
	</div>
<@~ end @>