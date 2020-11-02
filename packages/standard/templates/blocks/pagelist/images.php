<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ set { :hideThumbnails: @{ checkboxHideThumbnails } } @>
<div class="masonry<@ if @{ :pagelistDisplayCount } > 3 @> am-stretched<@ end @> @{ :classes }">
	<@ foreach in pagelist ~@>
		<@~ ../../snippets/set_imageteaser_variable.php @>
		<@~ if @{ :imageTeaser } @>
			<a href="@{ url }" class="masonry-item">
				<div class="masonry-content panel-image">
					<img src="@{ :imageTeaser }">
				</div>
			</a>
		<@ end @>
	<@~ end @>
</div>