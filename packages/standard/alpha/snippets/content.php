<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

	<div class="uk-block">
		<ul class="uk-grid grid-margin">
			<li class="uk-width-medium-1-3">
				<h1 class="uk-margin-small-bottom">@{ title }</h1>
				<div class="uk-text-muted"><@ ../../snippets/date.php @></div>
				<@ ../../snippets/tags.php @>
			</li>
			<@ if @{ textTeaser } @>
				<li class="content uk-width-medium-2-3">
					@{ textTeaser | markdown }
				</li>
			<@ end @>
		</ul>
	</div>
	<@ filelist { 
		glob: @{ imagesSlideshow | def('*.jpg, *.jpeg, *.png, *.gif') }, 
		sort: 'asc' 
	} @>
	<@ if @{ :filelistCount } @>
		<div class="uk-block block-full-width-small">
			<@ if @{ checkboxSingleImageSlideshow } @>
				<@ ../../snippets/slideshow.php @>
			<@ else @>
				<@ ../../snippets/slider.php @>
			<@ end @>
		</div>
	<@ end @>
	<@ ../../snippets/text_columns.php @>
	