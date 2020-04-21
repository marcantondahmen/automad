<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>
	<div class="content uk-block">
		<@ snippets/title.php @>
		<ul class="masonry uk-grid uk-grid-width-small-1-2">
			<@ with @{ imageProfile | def('*.jpg, *.jpeg, *.png, *.gif')} { width: 700 } @>
				<li>
					<img 
					src="@{ :fileResized }" 
					alt="@{ :basename }" 
					width="@{ :widthResized }" 
					height="@{ :heightResized }" 
					/>
				</li>
			<@ end @>
			<li class="content">
				<div class="uk-margin-small-bottom">
					@{ textTeaser | markdown }
				</div>
				@{ text | markdown }
			</li>
		</ul>
	</div>
<@ snippets/footer.php @>