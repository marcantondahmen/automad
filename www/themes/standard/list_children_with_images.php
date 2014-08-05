<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
@i(elements/header.php)
	
	<div class="container">
		
		<div class="row">
			<div id="title" class="col-md-6">
				<h1>@p(title)</h1>
				<h2>@p(subtitle)</h2>
			</div>	
			<div class="col-md-6">
				@p(text)
			</div>	
		</div>		
				
		<div class="row">
			@t(listConfig { type: "children" })
			<div class="col-md-6">
				@t(listFilters)
			</div>		
			<div class="col-md-6">
				@t(listSort {
					"Title": {
						sortItem: "title",
						sortOrder: "asc"
					},
					"Subtitle": {
						sortItem: "subtitle",
						sortOrder: "asc"
					}
				}) 
			</div>
		</div>	
		
		<div class="row">
			@t(listPages {
				variables: "title, subtitle",
				glob: "*.jpg, *.png",
				width: 350,
				height: 350,
				crop: true,
				class: "item image col-xs-6 col-sm-6 col-md-4 col-lg-3",
				firstWidth: 800,
				firstHeight: 800,
				firstClass: "item image col-xs-12 col-sm-12 col-md-8 col-lg-6"
			}) 
		</div>	
			
	</div>

@i(elements/footer.php)