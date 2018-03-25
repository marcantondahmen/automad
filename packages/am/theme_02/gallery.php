<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ elements/header.php @>

	<@ elements/teaser.php @>
	<@ filelist { glob: @{ imagesGallery }, sort: 'asc' } @>
	<ul class="am-msnry uk-grid uk-grid-width-medium-1-3">
		<@ foreach in filelist @>
			<li class="am-msnry-item">
				<a 
				href="<@ with @{ :file } { width: 1200 } @>@{ :fileResized }<@ end @>" 
				title="@{ :caption | stripTags }" 
				data-uk-lightbox="{group:'am-02-gallery'}"
				>
					<img 
					src="<@ with @{ :file } { width: 400 } @>@{ :fileResized }<@ end @>" 
					alt="@{ :basename }" 
					/>
				</a>
				<@ if @{ :caption } @>
					<div class="uk-margin-small-top uk-text-muted">
						@{ :caption | markdown }
					</div>
				<@ end @>
			</li>
		<@ end @>
	</ul>
	
<@ elements/footer.php @>