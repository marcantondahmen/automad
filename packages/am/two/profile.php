<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ elements/header.php @>

	<h1>@{ title }</h1>
	<ul class="uk-grid">
		<li class="am-two-content uk-width-medium-2-3">
			<div class="uk-margin-bottom uk-text-large uk-width-medium-9-10">
				@{ text | markdown }
			</div>
			<div class="uk-width-medium-9-10">
				@{ textTeaser | markdown }
			</div>
		</li>
		<li class="uk-width-medium-1-3">
			<@ with @{ imageProfile | def('*.jpg, *.jpeg, *.png, *.gif')} { width: 400 } @>	
				<img 
				src="@{ :fileResized }" 
				alt="@{ :basename }" 
				width="@{ :widthResized }" 
				height="@{ :heightResized }" 
				/>
			<@ end @>
		</li>	
	</ul>
	
<@ elements/footer.php @>