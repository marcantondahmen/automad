<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@~ ../../snippets/prev_next.php ~@>
<@ if not @{ checkboxHideTitle } ~@>
	<h1 class="uk-margin-top-remove">
		@{ title }
		<@ subtitle.php @>
	</h1>	
<@~ end @>