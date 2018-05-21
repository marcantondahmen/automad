<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

	<ul class="uk-grid uk-grid-width-small-1-2">
		<@ foreach in pagelist @>
			<li class="uk-block">
				<a 
				href="@{ url }" 
				class="uk-panel uk-panel-box uk-panel-box-hover
				">
					<@ with @{ imageTeaser | def('*.jpg, *.jpeg, *.png, *.gif') } {
						height: 500, 
						width: 750,
						crop: true
					} @>
						<div class="uk-panel-teaser">
							<img 
							src="@{ :fileResized }" 
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