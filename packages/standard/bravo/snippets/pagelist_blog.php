<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ set { :hideThumbnails: @{ checkboxHideThumbnails } } @>
<div class="masonry masonry-large<@ if @{ :pagelistCount } > 2 @> am-stretched<@ end @>">
	<@ foreach in pagelist ~@>
		<div class="masonry-item">
			<div class="masonry-content uk-panel uk-panel-box">
				<@ if not @{ :hideThumbnails } @>
					<@~ ../../snippets/set_imageteaser_variable.php @>
					<@ if @{ :imageTeaser } ~@>
						<div class="uk-panel-teaser">
							<a href="@{ url }"><img src="@{ :imageTeaser }"></a>
						</div>
					<@~ end ~@>
				<@ end @>
				<div class="uk-panel-title">
					<a href="@{ url }" class="nav-link">@{ title }</a>
					<div class="text-subtitle">
						<@ ../../snippets/date.php @>
						<@ if @{ date } and @{ tags } @><br><@ end @>
						<@ tags.php @>
					</div>
				</div>
				<@~ ../../snippets/set_teaser_variable.php @>
				<p class="content uk-margin-bottom-remove">@{ :teaser }</p>
				<@ more.php @>
			</div>
		</div>
	<@ else @>
		<div class="masonry-item">
			<div class="masonry-content uk-panel uk-panel-box">
				<div class="uk-panel-title uk-margin-remove">
					@{ notificationNoSearchResults | def ('No Pages Found') }
				</div>
			</div>
		</div>
	<@~ end @>
</div>