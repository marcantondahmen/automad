<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>

	<ul class="uk-grid uk-grid-width-medium-1-2">
		<li class="uk-block">
			<h1 class="uk-margin-small-bottom">@{ title }</h1>
			<@ ../snippets/date.php @>
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
					<@ ../snippets/slideshow.php @>
				</div>
			</div>
		</div>
	<@ end @>
	<div class="content uk-block uk-column-large-1-2">
		@{ text | markdown }
	</div>
	<# Related pages. #>
	<@ newPagelist { type: 'related' } @>
	<@ if @{ :pagelistCount } @>
		<div class="uk-block uk-margin-top">
			<ul class="uk-grid uk-grid-width-small-1-2">
				<@ foreach in pagelist @>
					<li class="uk-block">
						<div class="uk-panel uk-panel-box">
							<div class="uk-panel-title">
								@{ title }
							</div>
							<div class="uk-text-small">
								<@ ../snippets/date.php @>
							</div>
							<@ with @{ imageTeaser | def('*.jpg, *.png, *.gif') } { 
								height: 520,
								width: 780,
								crop: true 
							} @>
								<a 
								href="@{ url }" 
								class="uk-panel-teaser uk-margin-small-top uk-display-block"
								>
									<img src="@{ :fileResized }" alt="@{ :basename }">
								</a>
							<@ end @>
							<@ if @{ textTeaser } @>
								<div class="content uk-margin-small-top">
									@{ textTeaser | markdown }
								</div>
							<@ end @>
							<a 
							href="@{ url }" 
							class="uk-button uk-button-small uk-margin-small-top"
							>
								<i class="uk-icon-plus"></i>&nbsp;
								More
							</a>
						</div>
					</li>	
				<@ end @>
			</ul>
		</div>
	<@ end @>
	<@ ../snippets/prev_next.php @>
	
<@ snippets/footer.php @>