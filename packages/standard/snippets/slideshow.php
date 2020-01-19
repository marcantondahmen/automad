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
						<@~ slideshow_image.php ~@>
					</li>
				<@ end @>
			</ul>
			<@ slideshow_nav.php @>
		</div>
