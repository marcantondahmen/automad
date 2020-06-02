<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>
	<div class="content uk-block">
		<@ snippets/content.php @>
		<@ snippets/pagelist_config.php @>
		<@ if not @{ checkboxHideFiltersAndSort } @>
			<div id="list" class="buttons-stacked uk-margin-bottom">
				<@ snippets/filters.php @>
				<@ snippets/sort.php @>
				<@ snippets/clear_search.php @>
			</div>
		<@ end @>
		<@ snippets/pagelist_portfolio.php @>
		<@ snippets/pagination.php @>
	</div>
<@ snippets/footer.php @>