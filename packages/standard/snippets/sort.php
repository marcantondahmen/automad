<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

	<div class="uk-button-dropdown" data-uk-dropdown>
		<button class="uk-button">
			<svg class="bi bi-arrow-up-down" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
				<path fill-rule="evenodd" d="M11 3.5a.5.5 0 01.5.5v9a.5.5 0 01-1 0V4a.5.5 0 01.5-.5z" clip-rule="evenodd"/>
				<path fill-rule="evenodd" d="M10.646 2.646a.5.5 0 01.708 0l3 3a.5.5 0 01-.708.708L11 3.707 8.354 6.354a.5.5 0 11-.708-.708l3-3zm-9 7a.5.5 0 01.708 0L5 12.293l2.646-2.647a.5.5 0 11.708.708l-3 3a.5.5 0 01-.708 0l-3-3a.5.5 0 010-.708z" clip-rule="evenodd"/>
				<path fill-rule="evenodd" d="M5 2.5a.5.5 0 01.5.5v9a.5.5 0 01-1 0V3a.5.5 0 01.5-.5z" clip-rule="evenodd"/>
			</svg>&nbsp;
			<@ if not @{ ?sort } or @{ ?sort } = 'date desc' ~@>
				@{ labelSortDateDesc | def('Recent First') }
			<@~ end @>
			<@~ if @{ ?sort } = 'date asc' ~@>
				@{ labelSortDateAsc | def('Chronological') }
			<@~ end @>
			<@~ if @{ ?sort } = 'title asc' ~@>
				@{ labelSortTitleAsc | def('Title A-Z') }
			<@~ end @>
			<@~ if @{ ?sort } = 'title desc' ~@>
				@{ labelSortTitleDesc | def('Title Z-A') }
			<@~ end ~@>
		</button>
		<div class="uk-dropdown uk-dropdown-small uk-text-left">
			<ul class="uk-nav uk-nav-dropdown">
				<li>
					<a href="?<@ queryStringMerge { sort: 'date desc', page: 1 } @>">
						@{ labelSortDateDesc | def('Recent First') }
					</a>	
				</li>	
				<li>
					<a href="?<@ queryStringMerge { sort: 'date asc', page: 1 } @>">
						@{ labelSortDateAsc | def('Chronological') }
					</a>	
				</li>
				<li>
					<a href="?<@ queryStringMerge { sort: 'title asc', page: 1 } @>">
						@{ labelSortTitleAsc | def('Title A-Z') }
					</a>	
				</li>
				<li>
					<a href="?<@ queryStringMerge { sort: 'title desc', page: 1 } @>">
						@{ labelSortTitleDesc | def('Title Z-A') }
					</a>
				</li>
			</ul>
		</div>
	</div>	