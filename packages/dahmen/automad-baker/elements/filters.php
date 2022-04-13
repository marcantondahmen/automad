<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ if not @{ checkboxHideFilters } @>
	<div class="uk-button-dropdown" data-uk-dropdown="{mode:'click'}">
		<button class="uk-button uk-button">
			<@~ if @{ ?filter } ~@>
				@{ ?filter }
			<@~ else ~@>
				@{ labelShowAll | def ('Show All') }
			<@~ end ~@>
			&nbsp;&nbsp;<i class="uk-icon-caret-down"></i>
		</button>
		<div class="uk-dropdown uk-dropdown-small uk-dropdown-scrollable uk-text-left">
			<ul class="uk-nav uk-nav-dropdown">
				<li>
					<# Also reset pagination! #>
					<a href="?<@ queryStringMerge { filter: false, page: 1 } @>">
						@{ labelShowAll | def ('Show All') }
						<@ if not @{ ?filter } @>&nbsp;✓<@ end @>
					</a>
				</li>
				<@ foreach in filters @>
					<li>
						<a href="?<@ queryStringMerge { filter: @{ :filter }, page: 1 } @>">
							@{ :filter }
							<@ if @{ ?filter } = @{ :filter } @>&nbsp;✓<@ end @>
						</a>
					</li>
				<@ end @>
			</ul>
		</div>
	</div>
<@ end @>