<@ post.php @>
<# 

This template extends the "post.php" template.
The "main" snippet is overriden to actually change the content of the page body.

#>
<@ snippet main @>
	<main class="content uk-block">
		<@ elements/content.php @>
		<@ elements/pagelist_config.php @>
		<@ if not @{ checkboxHideFiltersAndSort } @>
			<div id="list" class="am-block buttons-stacked uk-margin-bottom">
				<@ elements/filters.php @>
				<@ elements/sort.php @>
				<@ elements/clear_search.php @>
			</div>
		<@ end @>
		<section <@ if @{ :pagelistDisplayCount } < 4 @>class="am-block"<@ end @>>
			<@ if @{ checkboxUseAlternativePagelistLayout } @>
				<@ blocks/pagelist/portfolio_alt.php @>
			<@ else @>
				<@ blocks/pagelist/portfolio.php @>
			<@ end @>
		</section>
		<@ elements/pagination.php @>
		<@ elements/prev_next.php @>
	</main>
<@ end @>