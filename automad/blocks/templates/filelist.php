<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<nav>
	<ul>
		<@ foreach in filelist @>
			<li>
				<a href="@{ :file }">@{ :basename }</a>
			</li>
		<@ end @>
	</ul>
</nav>