<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
@i(elements/header.php)

	<div class="container">
		
		<div class="row">
			<div id="title" class="col-md-6">
				<h1>@p(title) (@t(listCount))</h1>
				<h2></h2>
			</div>		
		</div>			
		<div class="row">
			<div class="col-md-12">
				@t(listFilters)
			</div>
			@t(listPages {
				variables: "title, subtitle, text",
				class: "item text-only col-xs-12 col-sm-10 col-md-8"
			}) 
		</div>	
		
	</div>

@i(elements/footer.php)