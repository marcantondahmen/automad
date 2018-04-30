<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

<@ snippets/header.php @>	
	<@ snippets/navbar.php @>
		
	<section class="section">		
		<@ snippets/content.php @>
	</section>
	
	<section class="section">
		<div class="columns is-multiline is-8 is-variable">
			<@ newPagelist { type: 'children' } @>
			<@ foreach in pagelist @>
				<div class="column is-one-quarter">
					<hr />
					<div class="field is-size-4 has-text-weight-bold">
						@{ title }
					</div>
					<div class="field is-size-6">@{ textTeaser | stripTags | 120 }</div>
					<a href="@{ url }" class="button is-light">More</a>
					
				</div>
			<@ end @>
		</div>
	</section>

<@ snippets/footer.php @>