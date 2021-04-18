<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ elements/header.php @>
	<div class="uk-flex">
		<@ elements/sidebar.php @>
		<main class="uk-width-large-3-4">
			<div class="content uk-block sidebar-block">
				<@ elements/content.php @>
				<@ elements/related_simple.php @>
			</div>
		</main>
	</div>
<@ elements/footer.php @>