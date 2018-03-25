<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ elements/header.php @>

	<@ elements/teaser.php @>
	<@ elements/pagelist_config.php @>
	<@ if not @{ checkboxHideFiltersAndSort } @>
		<div class="uk-margin-top">
			<div class="am-01-buttons-stacked">
				<@ elements/filters.php @>
				<@ elements/sort.php @>
				<@ elements/search_title.php @>
			</div>
		</div>
	<@ end @>
	<div class="uk-margin-small-top">
		<@ elements/pagelist.php @>
		<@ elements/pagination.php @>
	</div>
	
<@ elements/footer.php @>