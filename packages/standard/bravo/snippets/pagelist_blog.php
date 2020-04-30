<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<div class="masonry masonry-large<@ if @{ :pagelistCount } > 0 @> am-stretched<@ end @>">
	<@ foreach in pagelist ~@>
		<div class="masonry-item uk-panel uk-panel-box">
			<div class="masonry-content">
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
		</div>
	<@ else @>
		<div class="masonry-item">
			<h4 class="masonry-content">
				@{ notificationNoSearchResults | def ('No Pages Found') }
			</h4>
		</div>
	<@~ end @>
</div>