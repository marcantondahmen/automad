<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

	<ul class="uk-grid uk-grid-width-medium-1-2">
		<li>
			<div class="uk-block">
				<h1 class="uk-margin-small-bottom">@{ title }</h1>
				<@ ../../snippets/date.php @>
				<@ ../../snippets/tags.php @>
			</div>
			<@ if @{ textTeaser } @>
				<div class="content uk-block">
					@{ textTeaser | markdown }
				</div>
			<@ end @>
		</li>
		<@ filelist { 
			glob: @{ imagesSlideshow | def('*.jpg, *.jpeg, *.png, *.gif') }, 
			sort: 'asc' 
		} @>
		<@ if @{ :filelistCount } @>
			<li class="uk-block">
				<div class="uk-panel uk-panel-box">	
					<div class="uk-panel-teaser">
						<@ ../../snippets/slideshow_portrait.php @>
					</div>
				</div>
			</li>
		<@ end @>
	</ul>
	<@ ../../snippets/text_columns.php @>
	