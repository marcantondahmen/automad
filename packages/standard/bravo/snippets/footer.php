<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
		
		</div> <# .uk-block #>
		<div class="footer uk-text-muted uk-margin-large-bottom uk-margin-top">
			<ul class="uk-grid grid-margin uk-grid-width-medium-1-2">
				<li>
					<# Show menu with pages with checked "checkboxShowInFooter". #>
					<@ newPagelist { excludeHidden: false } @>
					<@ foreach in pagelist @>
						<@ if @{ checkboxShowInFooter } @>
							<a href="@{ url }">@{ title }</a>
							<br />
						<@ end @>
					<@ end @>
					<a href="/dashboard">Sign in</a>
				</li>
				<li class="uk-text-right uk-text-left-small">
					<a href="/">
						<i class="uk-icon-copyright"></i>
						@{ :now | dateFormat('Y') } @{ sitename }
					</a>
				</li>
			</ul>
			
			<# Add optional footer items. #>
			@{ itemsFooter }
			
		</div>
	
	</div>
	
</body>
</html>