<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ set { :hideThumbnails: @{ checkboxHideThumbnails } } @>
<div class="masonry<@ if @{ :pagelistCount } > 3 @> am-stretched<@ end @>">
	<@ foreach in pagelist ~@>
		<div class="masonry-item" <@ colors_inline.php @>>
			<div class="masonry-content uk-panel uk-panel-box">
				<@ if not @{ :hideThumbnails } and not @{ pageIconSvg } @>
					<@~ set_imageteaser_variable.php @>
					<@~ if @{ :imageTeaser } @>
						<div class="uk-panel-teaser">
							<a href="@{ url }"><img src="@{ :imageTeaser }"></a>
						</div>
					<@~ end ~@>
				<@ end @>
				<div class="uk-panel-title uk-margin-bottom-remove">
					<a href="@{ url }">
						<@ icon.php @>
						@{ title }
					</a>
					<div class="text-subtitle">
						<@ date.php @>
						<@ if @{ date } and @{ tags } @><br><@ end @>
						<@ tags.php @>
					</div>
				</div>
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