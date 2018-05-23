<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>

	<@ snippets/teaser.php @>	
	<@ ../snippets/pagelist_config.php @>
	<@ if not @{ checkboxHideFilters } @>	
		<div class="buttons-stacked uk-margin-bottom">
			<@ ../snippets/filters.php @>
			<@ ../snippets/clear_search.php @>
		</div>
	<@ end @>
	<ul class="uk-grid uk-grid-width-medium-1-2">
		<@ foreach in pagelist @>
			<li class="uk-block">
				<div class="uk-panel uk-panel-box">
					<div class="uk-panel-title">
						@{ title }
					</div>
					<div class="uk-text-small">
						<@ ../snippets/date.php @>
					</div>
					<@ with @{ imageTeaser | def('*.jpg, *.jpeg, *.png, *.gif') } { 
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
					<a href="@{ url }" class="uk-button uk-button-small uk-margin-small-top">
						<i class="uk-icon-plus"></i>&nbsp;
						More
					</a>
				</div>
			</li>	
		<@ end @>
	</ul>
	<@ ../snippets/pagination.php @>
	
<@ snippets/footer.php @>