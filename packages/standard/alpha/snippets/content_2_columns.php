<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

	<ul class="uk-grid">
		<li class="uk-block uk-width-large-1-3">
			<ul class="uk-grid grid-margin uk-grid-width-1-1">
				<li>
					<h1 class="uk-margin-small-bottom">@{ title }</h1>
					<div class="uk-text-muted"><@ ../../snippets/date.php @></div>
					<@ ../../snippets/tags.php @>
				</li>
				<@ if @{ textTeaser } @>
					<li class="content">
						@{ textTeaser | markdown }
					</li>
				<@ end @>
			</ul>
		</li>
		<li class="uk-width-large-2-3">
			<@ filelist { 
				glob: @{ imagesSlideshow | def('*.jpg, *.jpeg, *.png, *.gif') }, 
				sort: 'asc' 
			} @>
			<@ if @{ :filelistCount } @>
				<div class="uk-block block-full-width-small">
					<@ ../../snippets/slideshow_portrait.php @>
				</div>
			<@ end @>
			<div class="content uk-block">
				@{ text | markdown }
			</div>
		</li>	
	</ul>
	