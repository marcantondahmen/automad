<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
		<div class="uk-block">
			<div class="footer uk-margin-bottom">
				<ul class="uk-grid uk-grid-width-medium-1-2" data-uk-grid-margin>
					<li>
						<# Show menu with pages with checked "checkboxShowInFooter". #>
						<@~ newPagelist { excludeHidden: false } @>
						<@~ foreach in pagelist @>
							<@~ if @{ checkboxShowInFooter } @>
								<a href="@{ url }">@{ title }</a>
								<br />
							<@~ end @>
						<@~ end @>
					</li>
					<li class="uk-text-right uk-text-left-small">
						<a href="/">
							&copy; @{ :now | dateFormat('Y') } @{ sitename }
						</a>
					</li>
				</ul>
				<# Add optional footer items. #>
				@{ itemsFooter }
			</div>
		</div>
	</div>
</body>
</html>