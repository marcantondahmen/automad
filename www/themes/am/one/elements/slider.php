<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

	<@ if @{ :filelistCount } @>
		<div class="uk-panel uk-panel-box">
			<div 
			class="uk-slidenav-position" 
			data-uk-slider="autoplay:true,autoplayInterval:5000,center:false,infinite:true"
			>
				<div class="uk-slider-container">
					<ul class="uk-slider">
						<@ foreach in filelist { height: @{ slideshowHeight | def(420)} } @>
							<# 
							Assuming a max container size of 1200px, 720px would be 6 of 10.
							That is the width limit for all images to be able to swap grid sizes from 10 to 6 on small devices.
							To create a responsive slider, on large devices, the classes ".uk-width-*-10" are being used,
							while on small devices, ".uk-width-*-6" are used.
							#>
							<@ with @{ :fileResized } { width: 720, crop: true } @>
								<@ with @{ :fileResized } { width: @{ :widthResized | /120 | floor | *120 }, crop: true } @>
									<li class="uk-width-@{ :widthResized | /120 | floor }-6 uk-width-medium-@{ :widthResized | /120 | floor }-10">
										<img 
										src="@{ :fileResized }" 
										alt="@{ :basename }" 
										width="@{ :widthResized }" 
										height="@{ :heightResized }" 
										/>
									</li>	
								<@ end @>
							<@ end @>
						<@ end @>
					</ul>
				</div>
				<a 
				href="" 
				class="uk-slidenav uk-slidenav-contrast uk-slidenav-previous" 
				data-uk-slider-item="previous"
				></a>
				<a 
				href="" 
				class="uk-slidenav uk-slidenav-contrast uk-slidenav-next" 
				data-uk-slider-item="next"
				></a>
			</div>
		</div>
	<@ end @>