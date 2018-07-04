<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>

	<div class="uk-block">
		<ul class="masonry grid-margin uk-grid uk-grid-width-small-1-2">
			<li>
				<h1>@{ title }</h1>
			</li>
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