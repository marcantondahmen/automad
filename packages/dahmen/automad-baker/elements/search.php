<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ if @{ urlSearchResults } @>
	<form 
	class="baker-search uk-form" 
	action="@{ urlSearchResults | def('/') }" 
	method="get" 
	data-baker-autocomplete-submit
	>	
		<div 
		class="uk-autocomplete" 
		data-uk-autocomplete="{source: autocomplete}"
		>
			<input 
			class="uk-form-controls uk-width-1-1" 
			title="" 
			name="search" 
			type="search" 
			placeholder="@{ placeholderSearch | def('Search') }" 
			required
			/>
		</div>
	</form>
<@ end @>