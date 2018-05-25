<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>

	<@ snippets/teaser.php @>	
	<@ ../snippets/pagelist_config.php @>
	<@ if not @{ checkboxHideFilters } @>	
		<div class="uk-grid uk-block">
			<div
			class="buttons-stacked uk-width-medium-1-2<@ 
			if not @{ checkboxHideTitle } and @{ textTeaser } 
			@> uk-push-1-2<@ 
			end @>"
			>
				<@ ../snippets/filters.php @>
				<@ ../snippets/sort.php @>
				<@ ../snippets/clear_search.php @>
			</div>
		</div>
	<@ end @>
	<div class="uk-block">
		<@ snippets/pagelist_blog.php @>
	</div>
	<@ ../snippets/pagination.php @>
	
<@ snippets/footer.php @>