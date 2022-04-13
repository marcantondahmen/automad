<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

	<@ if @{ :paginationCount } > 1 @>
		<div class="uk-margin-top uk-margin-bottom">
			<@ if @{ ?page | def(1) } > 1 @>
				<ul class="uk-pagination uk-display-inline-block">
					<li>
						<a href="?<@ queryStringMerge { page: @{ ?page | def(1) | -1 } } @>">
							←
						</a>
					</li>
				</ul><@ 
			end 
			@><ul
			class="uk-pagination uk-display-inline-block" 
			data-uk-pagination="{pages:@{ :paginationCount },currentPage:@{ ?page | def(1) | -1 },displayedPages:3}"
			></ul><@ 
			if @{ ?page | def(1) } < @{ :paginationCount } 
			@><ul class="uk-pagination uk-display-inline-block">
					<li>
						<a href="?<@ queryStringMerge { page: @{ ?page | def(1) | +1 } } @>">
							→
						</a>
					</li>
				</ul>
			<@ end @>
		</div>
	<@ end @>
	