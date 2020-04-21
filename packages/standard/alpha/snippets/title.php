<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@~ ../../snippets/prev_next.php ~@>
<@ if not @{ checkboxHideTitle } ~@>
	<h1>
		@{ title }
		<@ subtitle.php @>
	</h1>	
<@~ end @>