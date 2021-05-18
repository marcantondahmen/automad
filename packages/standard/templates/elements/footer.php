<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
		<footer class="uk-block">
			<@ prev_next.php @>
			<div class="am-block footer uk-margin-bottom">
				<ul class="uk-grid uk-grid-width-medium-1-2" data-uk-grid-margin>
					<li>
						<# @{ checkboxShowInFooter } #>
						<@~ newPagelist { 
							excludeHidden: false,
							match: '{ "checkboxShowInFooter": "/[^0]+/" }' 
						} @>
						<@~ foreach in pagelist @>
							<a href="@{ url }"><@ icon_title.php @></a><br />
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
		</footer>
	</div>
</body>
</html>