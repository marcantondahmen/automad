<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ elements/header.php @>

	<@ elements/teaser.php @>
	<@ elements/pagelist_config.php @>
	<@ if not @{ checkboxHideFiltersAndSort } @>
		<div class="uk-margin-large-top">
			<@ elements/filters.php @>
			<@ elements/sort.php @>
			<@ elements/search_title.php @>
		</div>
	<@ end @>
	<@ elements/pagelist.php @>
	<@ elements/pagination.php @>
	
<@ elements/footer.php @>