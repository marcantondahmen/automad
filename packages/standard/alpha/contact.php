<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>

	<ul class="uk-block uk-grid grid-margin">
		<li class="uk-width-medium-1-3">
			<h1>@{ title }</h1>
		</li>
		<li class="uk-width-medium-2-3">
			<@ if @{ textTeaser } @>
				<div class="uk-block uk-padding-top-remove content">
					@{ textTeaser | markdown }
				</div>
			<@ end @>
			<div class="uk-block">
				<@ ../snippets/email_form.php @>
			</div>
		</li>
	</ul>

<@ snippets/footer.php @>