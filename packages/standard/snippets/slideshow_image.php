<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

			<img 
			src="@{ :fileResized }" 
			alt="@{ :basename }" 
			width="@{ :widthResized }" 
			height="@{ :heightResized }" 
			/>
			<@~ if @{ :caption } ~@>
				<div class="uk-overlay-panel uk-overlay-background uk-overlay-fade uk-text-center">
					@{ :caption | markdown }
				</div>	
			<@~ end ~@>
