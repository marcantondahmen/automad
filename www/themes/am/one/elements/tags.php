<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
	
	<@ if @{ tags } @>
		<ul class="uk-subnav">
			<li class="uk-disabled">
				<span><i class="uk-icon-tags"></i></span>
			</li>	
			<@ foreach in tags @>
				<li>
					<a href="@{ urlTagLinkTarget | def('/') }?filter=@{ :tag }">
						@{ :tag }
					</a>
				</li>
			<@ end @>	
		</ul>
	<@ end @>
	