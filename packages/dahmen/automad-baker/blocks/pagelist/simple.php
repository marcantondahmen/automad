<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<div class="uk-margin-top">
	<@ foreach in pagelist @>
		<a href="@{ url }" class="baker-preview">
			<h3>
				<span class="uk-visible-small baker-preview-parent">
					<@ if @{ :level } > 1 @>
						<@ with @{ :parent } @>
							@{ title } &nbsp;⁄
							<br />
						<@ end @>
					<@ end @>
				</span>
				@{ title }
				<span class="uk-hidden-small baker-preview-parent">
					<@ if @{ :level } > 1 @>
						&mdash;
						<@ with @{ :parent } @>
							@{ title }
						<@ end @>
					<@ end @>
				</span>
			</h3>	
			@{ +main | findFirstParagraph | 300 }
		</a>
	<@ else @>
		<h3>@{ notificationNoSearchResults | def('Nothing found.') }</h3>
	<@ end @>
</div>
