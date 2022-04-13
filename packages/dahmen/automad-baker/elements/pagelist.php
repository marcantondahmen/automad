<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

<@~ newPagelist { 
	type: 'children', 
	context: @{ urlContextForPagelist },
	excludeCurrent: true,
	filter: @{ ?filter },
	match: '{"url": "#@{ filterPagelistByUrl }#"}',
	sort: @{ ?sort | def (@{ sortPagelist }) | def('date desc') },
	limit: @{ itemsPerPage | def(8) },
	page: @{ ?page | def(1) }
} ~@>

<@ if @{ checkboxShowAllPagesInPagelist } @>
	<@~ pagelist { type: false } ~@>
<@ end @>

<@~ if @{ ?search } ~@>
	<@ pagelist { 
		type: false,
		match: false, 
		search: @{ ?search }
	} @>
<@~ end ~@>

<@ search_results.php @>

<@ if not @{ checkboxHideFilters } or @{ ?search } @>
	<div class="am-block baker-buttons-stacked uk-margin-small-top uk-margin-bottom">
		<@ filters.php @>
		<@ if @{ ?search } @>
			<a 
			href="?<@ queryStringMerge { search: false } @>" 
			class="uk-button"
			>
				"@{ ?search }"&nbsp;&nbsp;✗
			</a>
		<@ end @>
	</div>
<@ end @>

<@ if @{ :pagelistGrid } @>
	<@ masonry.php @>
<@ else @>
	<@ ../blocks/pagelist/simple.php @>
<@ end @>
<@ pagination.php @>
<@ footer_nav.php @>

