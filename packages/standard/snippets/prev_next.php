<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
	
	<@ if not @{ checkboxHidePrevNextNav } @>
		<@ newPagelist { type: 'siblings' } @>
		<@ if @{ :pagelistCount } @>
			<ul class="uk-pagination">
				<@ with prev @>
					<li class="uk-pagination-previous uk-hidden-small">
						<a href="@{ url }">
							<i class="uk-icon-angle-left uk-icon-large"></i>&nbsp;&nbsp;
							@{ title }
						</a>
					</li>
				<@ end @>
				<@ with next @>
					<li class="uk-pagination-next">
						<a href="@{ url }">
							@{ title }&nbsp;&nbsp;
							<i class="uk-icon-angle-right uk-icon-large"></i>
						</a>
					</li>
				<@ end @>
			</ul>
		<@ end @>
	<@ end @>