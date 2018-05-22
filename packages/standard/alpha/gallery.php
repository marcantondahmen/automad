<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>

	<@ snippets/teaser.php @>
	<div class="uk-block">
		<ul class="masonry uk-grid uk-grid-width-small-1-2 uk-grid-width-medium-1-3">
			<@ filelist { glob: @{ imagesGallery }, sort: 'asc' } @>
			<@ foreach in filelist @>
				<li class="masonry-item">
					<div class="uk-panel uk-panel-box">
						<a 
						href="<@ with @{ :file } { width: 1200 } @>@{ :fileResized }<@ end @>" 
						title="@{ :caption | stripTags }" 
						data-uk-lightbox="{group:'gallery'}"
						>
							<img 
							src="<@ with @{ :file } { width: 400 } @>@{ :fileResized }<@ end @>" 
							alt="@{ :basename }" 
							/>
						</a>
						<@ if @{ :caption } @>
							<div class="uk-margin-small-top uk-text-small">
								@{ :caption | markdown }
							</div>
						<@ end @>
					</div>
				</li>
			<@ end @>
		</ul>
	</div>
	
<@ snippets/footer.php @>