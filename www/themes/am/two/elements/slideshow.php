<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

	<@ if @{ :filelistCount } @>
		<div 
		class="uk-slidenav-position uk-overlay-hover" 
		data-uk-slideshow="{autoplay:true,autoplayInterval:3000,animation:'scroll'}"
		>
			<ul class="uk-slideshow">
				<@ foreach in filelist { 
					width: 1100, 
					height: @{ slideshowHeight | def(640) }, 
					crop: true 
				} @>
					<li>
						<img 
						src="@{ :fileResized }" 
						alt="@{ :basename }" 
						width="@{ :widthResized }" 
						height="@{ :heightResized }" 
						/>
						<@ if @{ :caption } @>
							<div class="uk-overlay-panel uk-overlay-bottom uk-overlay-background uk-overlay-fade uk-text-center">
								@{ :caption | markdown }
							</div>	
						<@ end @>
					</li>
				<@ end @>
			</ul>
			<# Only show naviagtion for more than one image. #>
			<@ if @{ :filelistCount } > 1 @>
				<a 
				href="" 
				class="uk-slidenav uk-slidenav-contrast uk-slidenav-previous" 
				data-uk-slideshow-item="previous"
				></a>
				<a 
				href="" 
				class="uk-slidenav uk-slidenav-contrast uk-slidenav-next" 
				data-uk-slideshow-item="next"
				></a>
			<@ end @>
		</div>
	<@ end @>