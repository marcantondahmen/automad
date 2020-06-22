<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<section>
	<ul>
		<@ foreach in filelist @>
			<li>
				<a href="@{ :file }">@{ :basename }</a>
			</li>
		<@ end @>
	</ul>
</section>