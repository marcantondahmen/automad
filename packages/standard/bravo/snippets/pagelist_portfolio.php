<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

	<ul class="masonry uk-grid uk-grid-width-medium-1-2" data-uk-grid-margin>
		<@ foreach in pagelist @>
			<li class="masonry-item">
				<a 
				href="@{ url }" 
				class="uk-panel uk-panel-box uk-panel-box-hover
				">
					<@ with @{ imageTeaser | def('*.jpg, *.jpeg, *.png, *.gif') } {
						width: 780
					} @>
						<div class="uk-panel-teaser">
							<img 
							src="@{ :fileResized }" 
							alt="@{ :basename }"
							width="@{ :widthResized }" 
							height="@{ :heightResized }" 
							/>
						</div>		
					<@ end @>
					<div class="uk-panel-title">
						@{ title }
					</div>
					<span class="uk-text-small">
						<@ ../../snippets/date.php @>
					</span>
				</a>
			</li>
		<@ end @>
	</ul>	