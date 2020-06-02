<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ if @{ :paginationCount } > 1 ~@>
	<section>
		<ul
		class="uk-pagination uk-pagination-left uk-margin-top" 
		data-uk-pagination="{pages:@{ :paginationCount },currentPage:@{ ?page | def(1) | -1 }}"
		></ul>
	</section>	
<@~ end @>