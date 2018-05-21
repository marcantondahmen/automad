<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>

	<div class="uk-block">
		<ul class="uk-grid uk-grid-width-medium-1-2" data-uk-grid-margin>
			<li>
				<h1 class="uk-margin-small-bottom">@{ title }</h1>
				<@ ../snippets/tags.php @>
			</li>
			<@ if @{ textTeaser } @>
				<li class="content">
					@{ textTeaser | markdown }
				</li>
			<@ end @>
		</ul>
	</div>
	<div class="uk-block">
		<ul class="masonry uk-grid uk-grid-width-small-1-2 uk-grid-width-medium-1-3 uk-grid-width-large-1-4">
			<@ filelist { glob: @{ imagesGallery }, sort: 'asc' } @>
			<@ foreach in filelist @>
				<li class="masonry-item">
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
									class="uk-overlay-panel uk-overlay-background uk-overlay-fade"
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