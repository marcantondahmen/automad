<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
	<div class="uk-button-dropdown" data-uk-dropdown>
		<button class="uk-button">
			<svg class="bi bi-funnel" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
  				<path fill-rule="evenodd" d="M1.5 1.5A.5.5 0 012 1h12a.5.5 0 01.5.5v2a.5.5 0 01-.128.334L10 8.692V13.5a.5.5 0 01-.342.474l-3 1A.5.5 0 016 14.5V8.692L1.628 3.834A.5.5 0 011.5 3.5v-2zm1 .5v1.308l4.372 4.858A.5.5 0 017 8.5v5.306l2-.666V8.5a.5.5 0 01.128-.334L13.5 3.308V2h-11z" clip-rule="evenodd"/>
			</svg>&nbsp;
			<@ if @{ ?filter } ~@>
				@{ ?filter }
			<@~ else ~@>
				@{ labelShowAll | def ('Show All') }
			<@~ end @>
		</button>
		<div class="uk-dropdown uk-dropdown-small uk-text-left">
			<ul class="uk-nav uk-nav-dropdown">
				<li>
					<# Also reset pagination! #>
					<a href="?<@ queryStringMerge { filter: false, page: 1 } @>#list">
						@{ labelShowAll | def ('Show All') }
					</a>
				</li>
				<@~ foreach in filters @>
					<li>
						<a href="?<@ queryStringMerge { filter: @{ :filter }, page: 1 } @>#list">
							@{ :filter }
						</a>
					</li>
				<@~ end @>
			</ul>
		</div>
	</div>