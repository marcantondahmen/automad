<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ elements/header.php @>

		<ul class="uk-grid">
			<li class="uk-width-1-1 baker-navbar-push">
				<@ set { :gridSize: 'large' } @>
				<@ if @{ checkboxSmallPagelistGrid } @>
					<@ set { :gridSize: 'small' } @>
				<@ end @>
				<@ elements/post_body.php @>
			</li>
		</ul>

<@ elements/footer.php @>