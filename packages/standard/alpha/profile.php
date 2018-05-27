<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>

	<div class="uk-block">
		<ul class="masonry grid-margin uk-grid uk-grid-width-medium-1-2">
			<li class="masonry-item">
				<h1>@{ title }</h1>
			</li>
			<@ with @{ imageProfile | def('*.jpg, *.jpeg, *.png, *.gif')} { width: 700 } @>
				<li class="masonry-item">
					<img 
					src="@{ :fileResized }" 
					alt="@{ :basename }" 
					width="@{ :widthResized }" 
					height="@{ :heightResized }" 
					/>
				</li>
			<@ end @>
			<li class="masonry-item content">
				<div class="uk-margin-small-bottom">
					@{ textTeaser | markdown }
				</div>
				@{ text | markdown }
			</li>
		</ul>
	</div>

<@ snippets/footer.php @>