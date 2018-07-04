<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

		<@ foreach in pagelist @>
			<div class="uk-block">
				<ul class="grid-margin uk-grid uk-grid-width-small-1-2 <@ 
				with @{ imageTeaser | def('*.jpg, *.jpeg, *.png, *.gif') } 
				@>masonry<@ 
				end @>">
					<li>
						<a href="@{ url }">
							<h3>@{ title }</h3>
						</a>
						<@ ../../snippets/date.php @>
						<@ ../../snippets/tags.php @>
					</li>
					<li>
						<@ with @{ imageTeaser | def('*.jpg, *.jpeg, *.png, *.gif') } { 
							width: 690
						} @>
							<div class="uk-panel uk-panel-box">
								<a 
								href="@{ url }" 
								class="uk-panel-teaser uk-display-block"
								>
									<img 
									src="@{ :fileResized }" 
									alt="@{ :basename }"
									width="@{ :widthResized }" 
									height="@{ :heightResized }" 
									>
								</a>
							</div>
						<@ end @>
					</li>
					<li>
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