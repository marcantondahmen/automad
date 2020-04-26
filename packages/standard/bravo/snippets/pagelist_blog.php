<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

<@~ if @{ :pagelistCount } > 1 ~@>
	<@ set { :classes: 'masonry am-stretched uk-grid uk-grid-width-large-1-2' } @>
<@~ else ~@>
	<@ set { :classes: 'uk-grid uk-grid-width-1-1' } @>
<@~ end ~@>

<ul class="@{ :classes }">
	<@ foreach in pagelist ~@>
		<li>
			<div class="uk-panel uk-panel-box">
				<div class="uk-panel-title">
					<a href="@{ url }" class="nav-link">@{ title }</a>
					<@ subtitle.php @>
				</div>
				<@~ ../../snippets/set_imageteaser_variable.php @>
				<@ if @{ :imageTeaser } ~@>
					<div class="uk-panel-teaser">
						<a href="@{ url }"><img src="@{ :imageTeaser }"></a>
					</div>
				<@~ end @>
				<@~ ../../snippets/set_teaser_variable.php @>
				<p class="content uk-margin-bottom-remove">@{ :teaser }</p>
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