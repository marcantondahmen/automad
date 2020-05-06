<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
	<div class="uk-button-dropdown" data-uk-dropdown>
		<button class="uk-button">
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
					<a href="?<@ queryStringMerge { sort: 'date desc', page: 1 } @>#list">
						@{ labelSortDateDesc | def('Recent First') }
					</a>	
				</li>	
				<li>
					<a href="?<@ queryStringMerge { sort: 'date asc', page: 1 } @>#list">
						@{ labelSortDateAsc | def('Chronological') }
					</a>	
				</li>
				<li>
					<a href="?<@ queryStringMerge { sort: 'title asc', page: 1 } @>#list">
						@{ labelSortTitleAsc | def('Title A-Z') }
					</a>	
				</li>
				<li>
					<a href="?<@ queryStringMerge { sort: 'title desc', page: 1 } @>#list">
						@{ labelSortTitleDesc | def('Title Z-A') }
					</a>
				</li>
			</ul>
		</div>
	</div>	