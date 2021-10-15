<@ post.php @>
<# 

This template extends the "post.php" template.
The "main" snippet is overriden to actually change the content of the page body.

#>
<@ snippet main @>
	<main class="content uk-block">
		<@ elements/content.php @>
		<@ elements/pagelist_config.php @>
		<@~ if not @{ checkboxHideFilters } @>
			<div id="list" class="am-block buttons-stacked uk-margin-bottom">
				<@ elements/filters.php @>
				<@ elements/clear_search.php @>
			</div>
		<@ end ~@>
		<@ blocks/pagelist/blog_classic.php @>
		<@ elements/pagination.php @>
		<@ elements/prev_next.php @>
	</main>
<@ end @>