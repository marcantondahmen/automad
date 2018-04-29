<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>

	<@ ../snippets/prev_next.php @>
	<h1>@{ title }</h1>
	<div class="uk-text-muted uk-margin-small-top uk-margin-large-bottom">
		<span class="uk-text-muted">@{ date | dateFormat('F Y') }</span>
		<@ ../snippets/tags.php @>
	</div>
	<@ filelist { 
		glob: @{ imagesSlideshow | def('*.jpg, *.jpeg, *.png, *.gif') }, 
		sort: 'asc' 
	} @>
	<@ if @{ :filelistCount } @>
		<@ if @{ checkboxSingleImageSlideshow } @>
			<@ ../snippets/slideshow.php @>
		<@ else @>
			<@ ../snippets/slider.php @>
		<@ end @>
	<@ end @>
	<div class="content uk-text-large uk-margin-top">
		@{ textTeaser | markdown }
	</div>
	<div class="content uk-margin-top uk-margin-large-bottom uk-column-large-1-2">
		@{ text | markdown }
	</div>
	<@ newPagelist { type: 'related' } @>
	<@ if @{ :pagelistCount } @>
		<h2 class="uk-margin-large-top">Related</h2>
		<@ snippets/pagelist.php @>
	<@ end @>
	
<@ snippets/footer.php @>