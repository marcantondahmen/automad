<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ elements/header.php @>
	<div class="am-container content uk-block">
		<@ elements/content.php @>
		<@ elements/pagelist_config.php @>
		<@ if not @{ checkboxHideFiltersAndSort } @>
			<div id="list" class="buttons-stacked uk-margin-bottom">
				<@ elements/filters.php @>
				<@ elements/sort.php @>
				<@ elements/clear_search.php @>
			</div>
		<@ end @>
		<section <@ if @{ :pagelistDisplayCount } > 3 @>class="cards-full-width"<@ end @>>
			<@ if @{ checkboxUseAlternativePagelistLayout } @>
				<@ blocks/pagelist/portfolio_alt.php @>
			<@ else @>
				<@ blocks/pagelist/portfolio.php @>
			<@ end @>
		</section>
		<@ elements/pagination.php @>
	</div>
<@ elements/footer.php @>