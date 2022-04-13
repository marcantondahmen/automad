<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ elements/header.php @>

		<ul class="uk-grid">
			<@ elements/sidebar.php @>
			<li class="uk-width-1-1 uk-width-large-3-4 baker-navbar-push">
				<@ elements/navbar.php @>
				<@ elements/title.php @>
				<div class="baker-content uk-margin-small-top">
					@{ +main }
					<@ set { 
						:pagelistGrid: false
					} @>
					<@ elements/pagelist.php @>
				</div>
			</li>
		</ul>

<@ elements/footer.php @>