<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ elements/header.php @>
	<main class="content uk-block">
		<@ elements/content.php @>
		<@ elements/pagelist_config.php @>
		<@~ if not @{ checkboxHideFilters } @>
			<div id="list" class="am-block buttons-stacked uk-margin-bottom">
				<@ elements/filters.php @>
				<@ elements/clear_search.php @>
			</div>
		<@ end ~@>
		<section <@ if @{ :pagelistDisplayCount } < 3 @>class="am-block"<@ end @>>
			<@ if @{ checkboxUseAlternativePagelistLayout } @>
				<@ blocks/pagelist/blog_alt.php @>
			<@ else @>
				<@ blocks/pagelist/blog.php @>
			<@ end @>
		</section>
		<@ elements/pagination.php @>
	</main>
<@ elements/footer.php @>