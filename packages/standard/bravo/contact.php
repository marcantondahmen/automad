<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>

	<div class="uk-block">
		<h1>@{ title }</h1>
	</div>
	<@ if @{ textTeaser } @>
		<div class="content uk-block">
			@{ textTeaser | markdown }
		</div>
	<@ end @>
	<div class="uk-block">
		<@ ../snippets/email_form.php @>
	</div>
	
<@ snippets/footer.php @>