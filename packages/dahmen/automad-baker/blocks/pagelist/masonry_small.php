<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ ../../elements/masonry_block_cache.php @>
<@ set { 
	:hideThumbnails: false,
	:teaserClass: 'not-full-width',
	:gridSize: 'small'
} @>
<@ ../../elements/masonry.php @>
<@ ../../elements/masonry_block_reset.php @>