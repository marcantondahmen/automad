<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

	<ul class="masonry uk-grid" data-uk-grid-margin>
		<@ foreach in pagelist @>
			<li class="masonry-item uk-width-small-1-2 uk-width-medium-1-3">
				<div class="uk-panel uk-panel-box">
					<div class="uk-panel-title">
						@{ title }
					</div>
					<div class="uk-text-muted">
						<@ ../../snippets/date.php @>
					</div>
					<@ with @{ imageTeaser | def('*.jpg, *.jpeg, *.png, *.gif') } { width: 480 } @>
						<a href="@{ url }" 
						class="uk-margin-small-top uk-display-block">
							<img 
							src="@{ :fileResized }" 
							alt="@{ :basename }"
							width="@{ :widthResized }" 
							height="@{ :heightResized }" 
							>
						</a>
					<@ end @>
					<@ if @{ textTeaser } @>
						<div class="content uk-margin-small-top">
							@{ textTeaser | markdown }
						</div>
					<@ end @>
					<a href="@{ url }" class="uk-button uk-margin-small-top">
						<i class="uk-icon-plus"></i>&nbsp;
						More
					</a>
				</div>
			</li>	
		<@ end @>
	</ul>