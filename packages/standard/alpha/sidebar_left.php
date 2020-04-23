<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>
	<div class="uk-flex">
		<div id="sidebar" class="uk-width-1-4 uk-visible-large">
			<div class="uk-block sidebar-wrapper">				
				<@ ../snippets/tree.php @>
			</div>
		</div>
		<div class="uk-width-large-3-4">
			<div class="content uk-block sidebar-block">
				<@ snippets/content.php @>
			</div>
		</div>
	</div>
<@ snippets/footer.php @>