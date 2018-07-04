<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>

	<div class="uk-block">
		<ul class="uk-grid grid-margin">
			<li class="uk-width-medium-1-3">
				<h1 class="uk-margin-small-bottom">@{ title }</h1>
				<div class="uk-text-muted"><@ ../snippets/date.php @></div>
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
		<div class="uk-block block-full-width-small">
			<@ ../snippets/slideshow.php @>
		</div>	
	<@ end @>
	<@ ../snippets/text_columns.php @>
	<# Related pages. #>
	<@ newPagelist { type: 'related', sort: 'date desc' } @>
	<@ if @{ :pagelistCount } @>
		<div class="uk-block">
			<h2>@{ labelRelated | def ('Related') }</h2>
			<@ snippets/pagelist_blog.php @>
		</div>
	<@ end @>
	<div class="uk-block">
		<@ ../snippets/prev_next.php @>
	</div>
	
<@ snippets/footer.php @>