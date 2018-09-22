<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

		<@ foreach in pagelist @>
			<div class="uk-block">
				<ul class="uk-grid">
					<li class="uk-width-small-1-3 uk-margin-bottom">
						<a href="@{ url }">
							<h3>@{ title }</h3>
						</a>
						<div class="uk-text-muted">
							<@ ../../snippets/date.php @>
							<@ ../../snippets/tags.php @>
						</div>
					</li>
					<li class="uk-width-small-2-3">
						<@ with @{ imageTeaser | def('*.jpg, *.jpeg, *.png, *.gif') } { 
							width: 840 
						} @>
							<a href="@{ url }" 
							class="uk-margin-bottom uk-display-block">
								<img 
								src="@{ :fileResized }" 
								alt="@{ :basename }"
								width="@{ :widthResized }" 
								height="@{ :heightResized }" 
								>
							</a>
						<@ end @>
						<@ if @{ textTeaser } @>
							<div class="content uk-margin-small-bottom">
								@{ textTeaser | markdown }
							</div>
						<@ end @>
						<a href="@{ url }" class="uk-button">
							<i class="uk-icon-share uk-icon-small"></i>
						</a>
					</li>
				</ul>
			</div>
		<@ end @>