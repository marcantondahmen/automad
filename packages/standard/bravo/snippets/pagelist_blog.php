<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

	<ul class="masonry uk-grid uk-grid-width-medium-1-2" data-uk-grid-margin>
		<@ foreach in pagelist @>
			<li class="masonry-item">
				<div class="uk-panel uk-panel-box">
					<div class="uk-panel-title">
						@{ title }
					</div>
					<div class="uk-text-small">
						<@ ../../snippets/date.php @>
					</div>
					<@ with @{ imageTeaser | def('*.jpg, *.jpeg, *.png, *.gif') } { 
						width: 780
					} @>
						<a 
						href="@{ url }" 
						class="uk-panel-teaser uk-margin-small-top uk-display-block"
						>
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
	