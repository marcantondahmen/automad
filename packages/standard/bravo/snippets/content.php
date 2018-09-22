<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

	<ul class="uk-grid uk-grid-width-medium-1-2">
		<li class="uk-block">
			<h1 class="uk-margin-small-bottom">@{ title }</h1>
			<@ ../../snippets/date.php @>
			<@ ../../snippets/tags.php @>
		</li>
		<@ if @{ textTeaser } @>
			<li class="content uk-block">
				@{ textTeaser | markdown }
			</li>
		<@ end @>
	</ul>
	<@ filelist { 
		glob: @{ imagesSlideshow | def('*.jpg, *.jpeg, *.png, *.gif') }, 
		sort: 'asc' 
	} @>
	<@ if @{ :filelistCount } @>
		<div class="uk-block">
			<div class="uk-panel uk-panel-box">	
				<div class="uk-panel-teaser">
					<@ if @{ checkboxSingleImageSlideshow } @>
						<@ ../../snippets/slideshow.php @>
					<@ else @>
						<@ ../../snippets/slider.php @>
					<@ end @>
				</div>
			</div>
		</div>
	<@ end @>
	<@ ../../snippets/text_columns.php @>
	