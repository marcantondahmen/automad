<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>

	<@ ../snippets/teaser.php @>
	<@ ../snippets/pagelist_config.php @>
	<@ if not @{ checkboxHideFiltersAndSort } @>
		<div class="uk-margin-large-top">
			<div class="buttons-stacked">
				<@ ../snippets/filters.php @>
				<@ ../snippets/sort.php @>
				<@ ../snippets/search_title.php @>
			</div>
		</div>
	<@ end @>
	<@ snippets/pagelist.php @>
	<@ ../snippets/pagination.php @>
	
<@ snippets/footer.php @>