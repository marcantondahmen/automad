<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>

	<div class="uk-block">
		<ul class="masonry uk-grid uk-grid-width-medium-1-2" data-uk-margin>
			<li class="masonry-item">
				<h1 class="uk-margin-small-bottom">@{ title }</h1>
			</li>
			<@ with @{ imageProfile | def('*.jpg, *.jpeg, *.png, *.gif')} { width: 600 } @>
				<li class="masonry-item uk-margin-small-bottom">
					<div class="uk-panel uk-panel-box">
						<img 
						src="@{ :fileResized }" 
						alt="@{ :basename }" 
						width="@{ :widthResized }" 
						height="@{ :heightResized }" 
						/>
					</div>
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