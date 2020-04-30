<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<div class="masonry<@ if @{ :pagelistCount } > 1 @> am-stretched<@ end @>">
	<@ foreach in pagelist ~@>
		<div class="masonry-item uk-panel uk-panel-box">
			<div class="masonry-content">
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