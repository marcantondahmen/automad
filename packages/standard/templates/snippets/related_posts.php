<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@~ newPagelist { type: 'related', sort: 'date desc' } @>	
<@~ if @{ :pagelistCount } @>
	<@ related.php @>
	<@ pagelist_blog.php @>
<@ end ~@>
	