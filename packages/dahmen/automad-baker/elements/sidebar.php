<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

		<li 
		id="baker-sidebar" 
		class="baker-sidebar uk-width-large-1-4 uk-visible-large"
		>	
			<div data-uk-sticky>
				<div class="baker-sidebar-scroll">
					<div class="baker-navbar-push">
						<@ logo.php @>
						<div data-baker-nav="@{ url }">
							<# The navigation gets cloned using JS from the hidden #baker-nav in the footer. #>
						</div>
					</div>
				</div>
			</div>
		</li>
