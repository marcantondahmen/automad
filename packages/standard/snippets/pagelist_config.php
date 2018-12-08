<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
	
	<@ newPagelist { 
		type: 'children', 
		context: @{ showPagesBelow },
		filter: @{ ?filter }, 
		search: @{ ?search },
		sort: @{ ?sort | def('date desc') },
		limit: @{ itemsPerPage | def(10) },
		page: @{ ?page | def(1) }
	} @>
	<@ if @{ checkboxShowAllPagesInPagelist } or @{ ?search } @>
		<@ pagelist { type: false } @>
	<@ end @>