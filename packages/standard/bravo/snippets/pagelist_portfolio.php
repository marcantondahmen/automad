<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

	<ul class="masonry grid-margin uk-grid uk-grid-width-small-1-2">
		<@ foreach in pagelist @>
			<li>
				<a 
				href="@{ url }" 
				class="uk-panel uk-panel-box uk-panel-box-hover
				">
					<@ with @{ imageTeaser | def('*.jpg, *.jpeg, *.png, *.gif') } {
						width: 690
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
					<@ ../../snippets/date.php @>
				</a>
			</li>
		<@ else @>
			<li>
				<h2>@{ notificationNoSearchResults | def('No pages found.')}</h2>
			</li>
		<@ end @>
	</ul>	