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
				<@ more.php @>
			</div>
		</li>
	<@ else @>
		<li>
			<h4>@{ notificationNoSearchResults | def ('No Pages Found') }</h4>
		</li>
	<@~ end @>
</ul>