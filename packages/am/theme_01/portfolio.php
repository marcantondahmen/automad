<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ elements/header.php @>

	<@ ../snippets/teaser.php @>
	<@ ../snippets/pagelist_config.php @>
	<@ if not @{ checkboxHideFiltersAndSort } @>
		<div class="uk-margin-top">
			<div class="buttons-stacked">
				<@ ../snippets/filters.php @>
				<@ ../snippets/sort.php @>
				<@ ../snippets/search_title.php @>
			</div>
		</div>
	<@ end @>
	<div class="uk-margin-small-top">
		<@ elements/pagelist.php @>
		<@ ../snippets/pagination.php @>
	</div>
	
<@ elements/footer.php @>