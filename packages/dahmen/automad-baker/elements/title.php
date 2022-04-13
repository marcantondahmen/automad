<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ if not @{ checkboxHideTitle } @>
	<div class="am-block uk-margin-small-bottom uk-margin-top-remove">
		<h1>@{ title }</h1>
		<@ tags.php @>	
		<@ if not @{ checkboxHideDate } and @{ date } @>
			<div class="uk-margin-small-bottom uk-margin-small-top">@{ date | dateFormat (@{ formatDate | def ('l, F jS Y')}, @{ locale }) }</div>
		<@ end @>
	</div>
<@ end @>