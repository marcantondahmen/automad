<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
	
	<@ if @{ tags } @>
		<ul class="uk-subnav">
			<@ foreach in tags @>
				<@ if @{ :i } > 1 @>
					<li class="uk-disabled">
						<span>,&nbsp;</span>
					</li>
				<@ end @>
				<li>
					<a href="@{ urlTagLinkTarget | def('/') }?filter=@{ :tag }">
						@{ :tag }
					</a>
				</li>
			<@ end @>	
		</ul>
	<@ end @>
	