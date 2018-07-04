<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>	
	<# Subdivide text into 2 columns in case the text is long enough on large screens. #>
	<div class="content uk-block<@ 
	if @{ text | striptags | strlen } > 500 @> uk-column-large-1-2<@ 
	else @> uk-width-large-1-2<@ 
	end @>">
		@{ text | markdown }
	</div>
	