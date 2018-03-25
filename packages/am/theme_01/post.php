<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ elements/header.php @>

	<@ elements/prev_next.php @>
	<h1 class="uk-margin-small-bottom">@{ title }</h1>
	<@ elements/date.php @>
	<@ elements/tags.php @>
	<ul class="am-msnry uk-grid uk-margin-small-top">
		<li class="am-msnry-item am-msnry-item-large uk-width-large-2-3">
			<@ filelist { 
				glob: @{ imagesSlideshow | def('*.jpg, *.jpeg, *.png, *.gif') }, 
				sort: 'asc' 
			} @>
			<@ elements/slideshow.php @>
			<div class="am-01-content uk-width-large-9-10 uk-margin-bottom">
				<div class="uk-text-large uk-margin-small-top">
					@{ textTeaser | markdown }
				</div>	
				<div class="uk-margin-small-top">
					@{ text | markdown }
				</div>
			</div>
		</li>
		<# Related pages. #>
		<@ newPagelist { type: 'related' } @>
		<@ foreach in pagelist @>
			<li class="am-msnry-item uk-width-small-1-2 uk-width-large-1-3">
				<div class="uk-panel uk-panel-box">
					<h3>@{ title }</h3>
					<@ elements/date.php @>
					<@ with @{ imageTeaser | def('*.jpg, *.png, *.gif') } { width: 450 } @>
						<a 
						href="@{ url }" 
						class="uk-margin-small-top uk-display-block"
						>
							<img src="@{ :fileResized }" alt="@{ :basename }">
						</a>
					<@ end @>
					<@ if @{ textTeaser } @>
						<div class="am-01-content uk-text-muted uk-margin-small-top">
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
	<@ elements/pagination.php @>

<@ elements/footer.php @>