<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

		<@ foreach in pagelist @>
			<div class="uk-block">
				<ul class="uk-grid" data-uk-margin>
					<li class="uk-width-medium-1-3">
						<a href="@{ url }">
							<h3>@{ title }</h3>
						</a>
						<div class="uk-text-muted">
							<@ ../../snippets/date.php @>
							<br />
							<@ foreach in tags 
							@><@ if @{ :i } > 1 @>, <@ end @>@{ :tag }<@ 
							end @>
						</div>
					</li>
					<li class="uk-width-medium-2-3">
						<@ with @{ imageTeaser | def('*.jpg, *.jpeg, *.png, *.gif') } { width: 850 } @>
							<a href="@{ url }" 
							class="uk-margin-small-bottom uk-display-block">
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
							<i class="uk-icon-plus-circle"></i>&nbsp;
							More
						</a>
					</li>
				</ul>
			</div>
		<@ end @>