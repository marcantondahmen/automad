<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@~ newPagelist { type: 'related', sort: @{ sortRelatedPages | def ('date desc') } } @>	
<@~ if @{ :pagelistCount } and not @{ checkboxHideRelatedPages } @>
	<@ related.php @>
	<div class="uk-margin-large-top">
		<@ ../blocks/pagelist/simple.php @>
	</div>
<@ end ~@>
	