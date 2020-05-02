<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ set { :hideThumbnails: @{ checkboxHideThumbnails } } @>
<div class="masonry<@ if @{ :pagelistCount } > 2 @> am-stretched<@ end @>">
	<@ foreach in pagelist ~@>
		<div class="masonry-item">
			<div class="masonry-content uk-panel uk-panel-box">
				<@ if not @{ :hideThumbnails } @>
					<@~ ../../snippets/set_imageteaser_variable.php @>
					<@~ if @{ :imageTeaser } @>
						<div class="uk-panel-teaser">
							<a href="@{ url }"><img src="@{ :imageTeaser }"></a>
						</div>
					<@~ end ~@>
				<@ end @>
				<div class="uk-panel-title uk-margin-bottom-remove">
					<a href="@{ url }">@{ title }</a>
					<div class="text-subtitle">
						<@ ../../snippets/date.php @>
						<@ if @{ date } and @{ tags } @><br><@ end @>
						<@ ../../snippets/tags.php @>
					</div>
				</div>
				<@ more.php @>
			</div>
		</div>
	<@ else @>
		<div class="masonry-item">
			<h4 class="masonry-content">
				@{ notificationNoSearchResults | def ('No Pages Found') }
			</h4>
		</div>
	<@~ end @>
</div>