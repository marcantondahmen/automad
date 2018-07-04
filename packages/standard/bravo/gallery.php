<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>

	<@ snippets/teaser.php @>
	<div class="uk-block">
		<ul 
		class="masonry grid-margin uk-grid uk-grid-width-small-1-2 uk-grid-width-medium-1-3 uk-grid-width-large-1-4">
			<@ filelist { glob: @{ imagesGallery }, sort: 'asc' } @>
			<@ foreach in filelist @>
				<li>
					<div class="uk-panel uk-panel-box">
						<a 
						href="<@ with @{ :file } { width: 1200 } @>@{ :fileResized }<@ end @>" 
						class="uk-panel-teaser uk-display-block"
						title="@{ :caption | stripTags }" 
						data-uk-lightbox="{group:'gallery'}"
						>
							<figure class="uk-overlay uk-overlay-hover">
								<img 
								src="<@ with @{ :file } { width: 500 } @>@{ :fileResized }<@ end @>" 
								alt="@{ :basename }" 
								/>
								<@ if @{ :caption } @>
									<figcaption 
									class="uk-overlay-panel uk-flex uk-flex-bottom uk-flex-center uk-text-center uk-overlay-background uk-overlay-fade"
									>
										@{ :caption | markdown }
									</figcaption>
								<@ end @>
							</figure>	
						</a>
					</div>
				</li>
			<@ end @>
		</ul>
	</div>
	
<@ snippets/footer.php @>