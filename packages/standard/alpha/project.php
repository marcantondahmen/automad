<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>

	<div class="uk-block">
		<ul class="uk-grid uk-grid-width-medium-1-2" data-uk-grid-margin>
			<li>
				<h1 class="uk-margin-small-bottom">@{ title }</h1>
				@{ date | dateFormat('F Y') }
				<@ ../snippets/tags.php @>
			</li>
			<@ if @{ textTeaser } @>
				<li class="content">
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
		<div class="uk-block">
			<div class="uk-panel uk-panel-box">	
				<@ if @{ checkboxSingleImageSlideshow } @>
					<@ ../snippets/slideshow.php @>
				<@ else @>
					<@ ../snippets/slider.php @>
				<@ end @>
			</div>
		</div>
	<@ end @>
	<div class="content uk-block uk-column-large-1-2">
		@{ text | markdown }
	</div>
	<div class="uk-block">
		<@ newPagelist { type: 'related' } @>
		<@ if @{ :pagelistCount } @>
			<h2 class="uk-margin-bottom">Related</h2>
			<@ snippets/pagelist.php @>
		<@ end @>
	</div>
	<@ ../snippets/prev_next.php @>
	
<@ snippets/footer.php @>