<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>

	<ul class="uk-grid uk-grid-width-medium-1-2">
		<li class="uk-block">
			<h1 class="uk-margin-small-bottom">@{ title }</h1>
			@{ date | dateFormat('F Y') }
			<@ ../snippets/tags.php @>
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
						<@ ../snippets/slideshow.php @>
					<@ else @>
						<@ ../snippets/slider.php @>
					<@ end @>
				</div>
			</div>
		</div>
	<@ end @>
	<@ ../snippets/text_columns.php @>
	<# Related pages. #>
	<@ newPagelist { type: 'related', sort: 'date desc' } @>
	<@ if @{ :pagelistCount } @>
		<div class="uk-block">
			<h2 class="uk-margin-bottom">@{ labelRelated | def ('Related') }</h2>
			<@ snippets/pagelist_portfolio.php @>
		</div>
	<@ end @>
	<div class="uk-block uk-margin-top">
		<@ ../snippets/prev_next.php @>
	</div>
	
<@ snippets/footer.php @>