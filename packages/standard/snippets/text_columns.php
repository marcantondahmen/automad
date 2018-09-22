<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>	

	<# Subdivide text into 2 columns in case the text is long enough on large screens. #>
	<div class="content uk-block<@ 
	if @{ text | striptags | strlen } > 300 @> uk-column-medium-1-2<@ 
	else @> uk-width-medium-1-2<@ 
	end @>">
		@{ text | markdown }
	</div>
	