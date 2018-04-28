<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
	
	<@ newPagelist { type: 'siblings' } @>
	<div class="slidenav uk-visible-large">
		<@ with prev @>
			<a 
			href="@{ url }" 
			class="uk-slidenav uk-slidenav-previous" 
			title="@{ title }" 
			data-uk-tooltip="{pos:'right',animation:true}"
			></a>
		<@ end @>
		<@ with next @>
			<a 
			href="@{ url }" 
			class="uk-slidenav uk-slidenav-next" 
			title="@{ title }" 
			data-uk-tooltip="{pos:'left',animation:true}"
			></a>
		<@ end @>
	</div>	