<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>

	<div class="uk-block">
		<ul class="uk-grid" data-uk-grid-margin>
			<li class="uk-width-medium-1-3">
				<h1>@{ title }</h1>
				<div class="uk-text-muted">@{ date | dateFormat('F Y') }</div>
				<@ ../snippets/tags.php @>
			</li>
			<@ if @{ textTeaser } @>
				<li class="content uk-text-muted uk-width-medium-2-3">
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
			<@ if @{ checkboxSingleImageSlideshow } @>
				<@ ../snippets/slideshow.php @>
			<@ else @>
				<@ ../snippets/slider.php @>
			<@ end @>
		</div>
	<@ end @>
	<div class="content uk-block uk-column-large-1-2">
		@{ text | markdown }
	</div>
	<# Related pages. #>
	<@ newPagelist { type: 'related' } @>
	<@ if @{ :pagelistCount } @>
		<div class="uk-block">
			<h2 class="uk-margin-bottom">Related</h2>
			<@ snippets/pagelist_portfolio.php @>
		</div>
	<@ end @>
	<@ ../snippets/prev_next.php @>
	
<@ snippets/footer.php @>