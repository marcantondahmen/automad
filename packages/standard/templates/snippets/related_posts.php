<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@~ newPagelist { type: 'related', sort: @{ sortRelatedPages | def ('date desc') } } @>	
<@~ if @{ :pagelistCount } and not @{ checkboxHideRelatedPages } @>
	<@ related.php @>
	<@ if @{ checkboxUseAlternativePagelistLayout } @>
		<@ ../blocks/pagelist/blog_alt.php @>
	<@ else @>
		<@ ../blocks/pagelist/blog.php @>
	<@ end @>
<@ end ~@>
	