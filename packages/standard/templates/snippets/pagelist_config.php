<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
	
	<@~ newPagelist { 
		type: 'children', 
		context: @{ showPagesBelow },
		filter: @{ ?filter }, 
		match: '{"url": "#@{ filterPagelistByUrl }#"}',
		sort: @{ ?sort | def (@{ sortPagelist }) | def('date desc') },
		limit: @{ itemsPerPage | def(10) },
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