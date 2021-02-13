<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<nav>
	<@ foreach in pagelist @>
		<p>
			<a href="@{ url }"><b>@{ title }</b></a>
			<br>
			@{ tags }
		</p>
	<@ end @>
</nav>