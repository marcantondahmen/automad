<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>

	<ul class="masonry uk-grid uk-grid-width-medium-1-2">
		<li class="masonry-item uk-block">
			<h1>@{ title }</h1>
		</li>
		<@ with @{ imageProfile | def('*.jpg, *.jpeg, *.png, *.gif')} { width: 780 } @>
			<li class="masonry-item uk-block">
				<div class="uk-panel uk-panel-box">
					<div class="uk-panel-teaser">
						<img 
						src="@{ :fileResized }" 
						alt="@{ :basename }" 
						width="@{ :widthResized }" 
						height="@{ :heightResized }" 
						/>
					</div>
				</div>
			</li>
		<@ end @>
		<li class="masonry-item content uk-block">
			<div class="uk-margin-small-bottom">
				@{ textTeaser | markdown }
			</div>
			@{ text | markdown }
		</li>
	</ul>

<@ snippets/footer.php @>