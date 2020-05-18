<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@~ newPagelist { type: 'related', sort: 'date desc' } @>	
<@~ if @{ :pagelistCount } @>
	<@ related.php @>
	<div class="uk-margin-large-top">
		<@ pagelist_simple.php @>
	</div>
<@ end ~@>
	