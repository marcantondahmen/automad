<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ elements/header.php @>

	<@ elements/teaser.php @>
	<@ elements/pagelist_config.php @>
	<@ if not @{ checkboxHideFiltersAndSort } @>
		<div class="uk-margin-large-top">
			<div class="buttons-stacked">
				<@ elements/filters.php @>
				<@ elements/sort.php @>
				<@ elements/search_title.php @>
			</div>
		</div>
	<@ end @>
	<@ elements/pagelist.php @>
	<@ elements/pagination.php @>
	
<@ elements/footer.php @>