<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

	<ul class="masonry uk-grid uk-grid-width-small-1-2 uk-grid-width-medium-1-3 uk-margin-small-top">
		<@ foreach in pagelist @>
			<li class="masonry-item">
				<a href="@{ url }" class="uk-panel uk-panel-box">
					<@ with @{ imageTeaser | def('*.jpg, *.jpeg, *.png, *.gif') } { width: 500 } @>
						<img src="@{ :fileResized }" width="@{ :widthResized }" height="@{ :heightResized }" />
					<@ end @>
					<h3 class="uk-margin-small-top">
						@{ title }
					</h3>
					<@ date.php @>
				</a>
			</li>
		<@ end @>
	</ul>	