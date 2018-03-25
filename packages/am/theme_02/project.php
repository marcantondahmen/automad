<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ elements/header.php @>

	<@ elements/prev_next.php @>
	<h1>@{ title }</h1>
	<div class="uk-text-muted uk-margin-small-top uk-margin-large-bottom">
		<span class="uk-text-muted">@{ date | dateFormat('F Y') }</span>
		<@ elements/tags.php @>
	</div>
	<@ filelist { 
		glob: @{ imagesSlideshow | def('*.jpg, *.jpeg, *.png, *.gif') }, 
		sort: 'asc' 
	} @>
	<@ if @{ checkboxSingleImageSlideshow } @>
		<@ elements/slideshow.php @>
	<@ else @>
		<@ elements/slider.php @>
	<@ end @>
	<div class="am-02-content uk-text-large uk-margin-top">
		@{ textTeaser | markdown }
	</div>
	<div class="am-02-content uk-margin-top uk-margin-large-bottom uk-column-large-1-2">
		@{ text | markdown }
	</div>
	<@ newPagelist { type: 'related' } @>
	<@ if @{ :pagelistCount } @>
		<h2 class="uk-margin-large-top">Related</h2>
		<@ elements/pagelist.php @>
	<@ end @>
	
<@ elements/footer.php @>