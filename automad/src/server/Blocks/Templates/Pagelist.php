<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<ul>
	<@ foreach in pagelist @>
		<li>
			<a href="@{ url }">@{ title }</a>
		</li>
	<@ end @>
</ul>
