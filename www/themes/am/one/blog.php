<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ elements/header.php @>

	<@ elements/teaser.php @>
	<@ elements/pagelist_config.php @>
	<@ if not @{ checkboxHideFilters } @>
		<div class="uk-margin-top">
			<@ elements/filters.php @>
			<@ elements/search_title.php @>
		</div>
	<@ end @>
	<ul class="am-msnry uk-grid uk-margin-small-top">
		<@ foreach in pagelist @>
			<@ if @{ :i } = 1 @>
				<li class="am-msnry-item am-msnry-item-large uk-width-large-2-3">
					<div class="uk-panel uk-panel-box">
						<h3>@{ title }</h3>
						<@ elements/date.php @>
						<@ with @{ imageTeaser | def('*.jpg, *.jpeg, *.png, *.gif') } { width: 850 } @>
							<a 
							href="@{ url }" 
							class="uk-margin-small-top uk-display-block"
							>
								<img src="@{ :fileResized }" alt="@{ :basename }">
							</a>
						<@ end @>
						<@ if @{ textTeaser } @>
							<div class="am-one-content uk-text-muted uk-text-large uk-margin-small-top">
								@{ textTeaser | markdown }
							</div>
						<@ end @>
						<a href="@{ url }" class="uk-button uk-margin-small-top">
							<i class="uk-icon-plus"></i>&nbsp;
							More
						</a>
					</div>
				</li>
			<@ else @>
				<li class="am-msnry-item uk-width-small-1-2 uk-width-large-1-3">
					<div class="uk-panel uk-panel-box">
						<h3>@{ title }</h3>
						<@ elements/date.php @>
						<@ with @{ imageTeaser | def('*.jpg, *.jpeg, *.png, *.gif') } { width: 650 } @>
							<a href="@{ url }" class="uk-margin-small-top uk-display-block">
								<img src="@{ :fileResized }" alt="@{ :basename }">
							</a>
						<@ end @>
						<@ if @{ textTeaser } @>
							<div class="am-one-content uk-text-muted uk-margin-small-top">
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
		<@ end @>
	</ul>
	<@ elements/pagination.php @>

<@ elements/footer.php @>