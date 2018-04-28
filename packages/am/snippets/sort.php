<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

	<div class="uk-button-dropdown" data-uk-dropdown>
		<button class="uk-button">
			<i class="uk-icon-refresh"></i>&nbsp;
			Sorting
		</button>
		<div class="uk-dropdown uk-dropdown-small uk-text-left">
			<ul class="uk-nav uk-nav-dropdown">
				<li>
					<a href="?<@ queryStringMerge { sort: 'date desc', page: 1 } @>">
						<@ if not @{ ?sort } or @{ ?sort } = 'date desc' @>
							<i class="uk-icon-circle"></i>
						<@ else @>
							<i class="uk-icon-circle-o"></i>
						<@ end @>
						&nbsp;Newest On Top
					</a>	
				</li>	
				<li>
					<a href="?<@ queryStringMerge { sort: 'date asc', page: 1 } @>">
						<@ if @{ ?sort } = 'date asc' @>
							<i class="uk-icon-circle"></i>
						<@ else @>
							<i class="uk-icon-circle-o"></i>
						<@ end @>
						&nbsp;Oldest On Top
					</a>	
				</li>
				<li>
					<a href="?<@ queryStringMerge { sort: 'title asc', page: 1 } @>">
						<@ if @{ ?sort } = 'title asc' @>
							<i class="uk-icon-circle"></i>
						<@ else @>
							<i class="uk-icon-circle-o"></i>
						<@ end @>
						&nbsp;Title A-Z
					</a>	
				</li>
				<li>
					<a href="?<@ queryStringMerge { sort: 'title desc', page: 1 } @>">
						<@ if @{ ?sort } = 'title desc' @>
							<i class="uk-icon-circle"></i>
						<@ else @>
							<i class="uk-icon-circle-o"></i>
						<@ end @>
						&nbsp;Title Z-A
					</a>
				</li>
			</ul>
		</div>
	</div>	