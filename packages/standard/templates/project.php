<# 

This template extends the "post.php" template.
The "main" snippet is overriden to actually change the content of the page body.

#>
<@ snippet main @>
	<main class="content uk-block">
		<@ elements/content.php @>
	</main>
	<div class="content uk-block">
		<@ elements/related_projects.php @>
	</div>
<@ end @>
<@ post.php @>