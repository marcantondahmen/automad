<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>
	
	<@ snippets/teaser.php @>
	<@ ../snippets/pagelist_config.php @>
	<div class="uk-block">
		<@ if not @{ checkboxHideFilters } @>
			<div class="buttons-stacked uk-margin-bottom">
				<@ ../snippets/filters.php @>
				<@ ../snippets/clear_search.php @>
			</div>
		<@ end @>
		<ul class="masonry uk-grid">
			<@ foreach in pagelist @>
				<li class="masonry-item uk-width-small-1-2">
					<div class="uk-panel uk-panel-box">
						<div class="uk-panel-title">
							@{ title }
						</div>
						<div class="uk-text-small">
							<@ ../snippets/date.php @>
						</div>
						<@ with @{ imageTeaser | def('*.jpg, *.jpeg, *.png, *.gif') } { width: 600 } @>
							<a href="@{ url }" class="uk-margin-small-top uk-display-block">
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
	</div>
	
<@ snippets/footer.php @>