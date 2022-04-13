<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
	
	</div> <# uk-container #>	

	<# Include a hidden navigation as source for AJAX requests and to be cloned into both sidebars. #>
	<div id="baker-nav" class="uk-hidden">
		<@ nav.php @>
	</div>
	
	@{ itemsFooter }

</body>
</html>