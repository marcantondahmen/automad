<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>

	<div class="uk-block">
		<ul class="uk-grid" data-uk-grid-margin>
			<li class="uk-width-medium-1-3">
				<h1>@{ title }</h1>
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
		<div class="uk-block">
			<@ ../snippets/slideshow.php @>
		</div>	
	<@ end @>
	<div class="content uk-block">
		@{ text | markdown }
	</div>
	<# Related pages. #>
	<@ newPagelist { type: 'related' } @>
	<@ if @{ :pagelistCount } @>
		<div class="uk-block">
			<h2 class="uk-margin-large-bottom">Related</h2>
			<@ snippets/pagelist_blog.php @>
		</div>
	<@ end @>
	<@ ../snippets/prev_next.php @>
	
<@ snippets/footer.php @>