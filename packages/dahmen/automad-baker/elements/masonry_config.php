<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ set { 
	:pagelistGrid: true,
	:hideThumbnails: @{ checkboxHideThumbnails },
	:teaserClass: 'not-full-width'
} @>
<@ if @{ checkboxStretchThumbnails } @>
	<@ set { 
		:teaserClass: 'full-width'
	} @>
<@ end @>