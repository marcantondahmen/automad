<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>

	<@ ../snippets/pagelist_config.php @>
	<@ if not @{ checkboxHideTeaser } or not @{ checkboxHideFiltersAndSort } @>
		<div class="uk-block">
			<@ if not @{ checkboxHideTeaser } @>
				<ul class="uk-grid uk-grid-width-medium-1-2" data-uk-grid-margin>
					<@ if not @{ checkboxHideTitle } @>
						<li>
							<h1>@{ title }</h1>
						</li>
					<@ end @>
					<@ if @{ textTeaser } @>
						<li class="content">
							@{ textTeaser | markdown }
						</li>	
					<@ end @>
				</ul>
			<@ end @>	
		</div>
	<@ end @>
	<div class="uk-block">
		<@ if not @{ checkboxHideFiltersAndSort } @>
			<div class="buttons-stacked">
				<@ ../snippets/filters.php @>
				<@ ../snippets/sort.php @>
				<@ ../snippets/clear_search.php @>
			</div>
		<@ end @>
		<@ snippets/pagelist.php @>
		<@ ../snippets/pagination.php @>
	</div>
	
<@ snippets/footer.php @>