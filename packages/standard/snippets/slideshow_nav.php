<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

			<# Only show naviagtion for more than one image. #>
			<@~ if @{ :filelistCount } > 1 @>
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
				<ul class="uk-dotnav uk-dotnav-contrast uk-margin-small-bottom uk-position-bottom uk-flex-center">
					<@ for 0 to @{ :filelistCount | -1 } ~@>
						<li data-uk-slideshow-item="@{ :i }"><a href=""></a></li>
					<@~ end @>
				</ul>
			<@ end ~@>
		
