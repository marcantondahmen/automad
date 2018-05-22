<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>

	<@ snippets/teaser.php @>
	<@ ../snippets/pagelist_config.php @>
	<div class="uk-block">
		<@ if not @{ checkboxHideFiltersAndSort } @>
			<div class="buttons-stacked uk-margin-bottom">
				<@ ../snippets/filters.php @>
				<@ ../snippets/sort.php @>
				<@ ../snippets/clear_search.php @>
			</div>
		<@ end @>
		<@ snippets/pagelist.php @>
		<@ ../snippets/pagination.php @>
	</div>
	
<@ snippets/footer.php @>