<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>
	<div class="uk-flex">
		<div id="sidebar" class="uk-width-1-4 uk-visible-large">
			<div class="sidebar-wrapper">			
				<div class="uk-margin-top">
					<@ ../snippets/tree.php @>
				</div>
			</div>
		</div>
		<div class="uk-width-large-3-4">
			<div class="content uk-block">
				<@ snippets/content.php @>
			</div>
		</div>
	</div>
<@ snippets/footer.php @>