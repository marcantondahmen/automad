<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

		<div 
		class="uk-slidenav-position uk-overlay-hover" 
		data-uk-slideshow="{autoplay:true,autoplayInterval:3000,animation:'scroll'}"
		>
			<ul class="uk-slideshow">
				<@ foreach in filelist { 
					width: 1440, 
					height: @{ slideshowHeight | def(810) }, 
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
							<div class="uk-overlay-panel uk-overlay-background uk-overlay-fade uk-text-center">
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
				<ul class="uk-dotnav uk-dotnav-contrast uk-position-bottom uk-flex-center">
					<@ for 0 to @{ :filelistCount | -1 } @>
						<li data-uk-slideshow-item="@{ :i }"><a href=""></a></li>
					<@ end @>
				</ul>
			<@ end @>
		</div>
