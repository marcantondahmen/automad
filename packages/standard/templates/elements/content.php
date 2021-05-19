<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ ../snippets/title.php @>
<@ if not @{ +main } and @{ textTeaser | def (@{ text }) } @>
	@{ textTeaser | markdown }
	@{ text | markdown }
<@ else @>
	@{ +main }
<@ end @>