<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ set { :hideThumbnails: @{ checkboxHideThumbnails } } @>
<div class="@{ :classes | def('cards cards-large masonry') }<@ if @{ :pagelistDisplayCount } > 2 @> am-stretched<@ end @>">
	<@ foreach in pagelist ~@>
		<div class="card" <@ ../../snippets/colors_inline.php @>>
			<div class="card-content uk-panel uk-panel-box">
				<@ if not @{ :hideThumbnails } and not @{ pageIconSvg } @>
					<@~ ../../snippets/set_imageteaser_variable.php @>
					<@ if @{ :imageTeaser } ~@>
						<div class="uk-panel-teaser">
							<a href="@{ url }"><img src="@{ :imageTeaser }"></a>
						</div>
					<@~ end ~@>
				<@ end @>
				<div class="panel-body">
					<div class="uk-panel-title">
						<a href="@{ url }" class="nav-link">
							<@ ../../snippets/icon.php @>
							@{ title }
						</a>
						<div class="text-subtitle">
							<@ ../../snippets/date.php @>
							<@ if @{ date } and @{ tags } @><br><@ end @>
							<@ ../../snippets/tags.php @>
						</div>
					</div>
					<@~ ../../snippets/set_teaser_variable.php @>
					<p class="content uk-margin-bottom-remove">@{ :teaser }</p>
				</div>
				<@ ../../snippets/more.php @>
			</div>
		</div>
	<@ else @>
		<div class="card">
			<div class="card-content uk-panel uk-panel-box">
				<div class="uk-panel-title uk-margin-remove">
					@{ notificationNoSearchResults | def ('No Pages Found') }
				</div>
			</div>
		</div>
	<@~ end @>
</div>