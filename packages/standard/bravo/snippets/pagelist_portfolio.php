<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

<@~ if @{ :pagelistCount } > 2 ~@>
	<@ set { 
		:container: 'am-stretched masonry uk-grid uk-grid-width-small-1-2 uk-grid-width-large-1-3'
	} @>
<@~ else ~@>
	<@ set { 
		:container: 'uk-grid uk-grid-width-small-1-2'
	} @>
<@~ end ~@>

<ul class="@{ :container }">
	<@ foreach in pagelist ~@>
		<li>
			<div class="uk-panel uk-panel-box uk-height-1-1">
				<@~ ../../snippets/set_imageteaser_variable.php @>
				<@~ if @{ :imageTeaser } @>
					<div class="uk-panel-teaser">
						<a href="@{ url }"><img src="@{ :imageTeaser }"></a>
					</div>
				<@~ end @>
				<div class="uk-panel-title uk-margin-bottom-remove">
					<a href="@{ url }">@{ title }</a>
					<div class="text-subtitle">
						<@ ../../snippets/date.php @>
						<@ if @{ date } and @{ tags } @><br><@ end @>
						<@ ../../snippets/tags.php @>
					</div>
				</div>
				<a href="@{ url }" class="nav-link panel-more">
					<svg class="bi bi-plus-circle-fill" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
  						<path fill-rule="evenodd" d="M16 8A8 8 0 110 8a8 8 0 0116 0zM8.5 4a.5.5 0 00-1 0v3.5H4a.5.5 0 000 1h3.5V12a.5.5 0 001 0V8.5H12a.5.5 0 000-1H8.5V4z" clip-rule="evenodd"/>
					</svg>
				</a>
			</div>
		</li>
	<@ else @>
		<li>
			<h4>@{ notificationNoSearchResults | def ('No Pages Found') }</h4>
		</li>
	<@~ end @>
</ul>