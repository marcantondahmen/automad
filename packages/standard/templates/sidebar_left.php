<@ post.php @>
<# 

This template extends the "post.php" template.
The "main" snippet is overriden to actually change the content of the page body.

#>
<@ snippet main @>
	<div class="uk-flex">
		<@ elements/sidebar.php @>
		<main class="uk-width-large-3-4">
			<div class="content uk-block sidebar-block">
				<@ elements/content.php @>
				<@ elements/prev_next.php @>
				<@ elements/related_simple.php @>
			</div>
		</main>
	</div>
<@ end @>