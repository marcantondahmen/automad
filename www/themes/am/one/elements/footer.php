<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

		<div class="am-one-footer uk-text-muted uk-margin-large-bottom uk-margin-top">
			<ul class="uk-grid uk-grid-large uk-grid-width-medium-1-3">
				<li class="uk-margin-top">
					<a href="/">
						<i class="uk-icon-copyright"></i>
						@{ :now | dateFormat('Y') } @{ sitename }
					</a>
				</li>
				<li class="uk-margin-top">
					<# Show menu with pages with checked "checkboxShowInFooter". #>
					<@ newPagelist { excludeHidden: false } @>
					<@ foreach in pagelist @>
						<@ if @{ checkboxShowInFooter } @>
							<a href="@{ url }">@{ title }</a>
							<br />
						<@ end @>
					<@ end @>
				</li>
				<li class="uk-margin-top">
					<a href="/dashboard">Sign in</a>
				</li>
			</ul>
		</div>
	
		<# Add optional footer items. #>
		@{ itemsFooter }
	
	</div>
	
</body>
</html>