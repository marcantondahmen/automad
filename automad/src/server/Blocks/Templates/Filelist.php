<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<ul>
	<@ foreach in filelist @>
		<li>
			<a href="@{ :file }">@{ :basename }</a>
		</li>
	<@ end @>
</ul>