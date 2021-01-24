<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ set { :hideThumbnails: @{ checkboxHideThumbnails } } @>
<div class="@{ :classes | def('cards masonry clean') }">
	<@ foreach in pagelist ~@>
		<@~ ../../elements/set_imageteaser_variable.php @>
		<@~ if @{ :imageTeaser } @>
			<a href="@{ url }" class="card">
				<div class="card-content panel-image">
					<img src="@{ :imageTeaser }">
				</div>
			</a>
		<@ end @>
	<@~ end @>
</div>