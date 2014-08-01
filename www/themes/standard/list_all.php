<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
@i(elements/header.php)
	
	<div class="container">
		
		<div class="row">
			<div id="title" class="col-md-12">
				<h1>@p(title)</h1>
				<h2>@p(subtitle)</h2>
			</div>	
			<div class="col-md-8">
				@p(text)
			</div>	
		</div>		
				
		<div class="row">
			<div class="col-md-12">
				@t(listFilters)
				@t(listSort {
					"Ascending": {
						sortItem: "title",
						sortOrder: "asc"
					},
					"Descending": {
						sortItem: "title",
						sortOrder: "desc"
					}
				}) 
			</div>
		</div>	
		
		<div class="row">
			@t(listPages {
				variables: "title, subtitle, text",
				class: "item text-only col-xs-12 col-sm-10 col-md-8"
			}) 
		</div>	
			
	</div>

@i(elements/footer.php)