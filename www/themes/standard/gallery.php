<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
@i(elements/header.php)

	<div class="container">
		
		<div class="row">
			<div id="title" class="col-md-12">
				<h1>@p(title)</h1>
				<h2>@p(subtitle)</h2>
			</div>
			<div class="col-md-12">
				@t(filterParentByTags)
			</div>
			<div class="col-md-12 no-vertical-padding">
				<div class="row">
					@x(Gallery {
						files: @p(gallery_files),
						width: 240,
						height: 240,
						class: "col-xs-4 col-sm-3 col-md-2 col-lg-2",
						firstWidth: 480,
						firstHeight: 480,
						firstClass: "col-xs-8 col-sm-6 col-md-4 col-lg-4",
						enlargedWidth: 1200,
						enlargedHeight: 900
					})
				</div>
			</div>
			<div class="col-md-6">
				@p(text)
			</div>
			<div class="col-md-6">
				@p(text_2)
			</div>	
		</div>		
		
		<hr>
			
		<div class="row">
			@t(listConfig {type: "related" })
			@t(listPages {
				variables: "title, subtitle",
				crop: true,
				class: "text-only col-xs-6 col-sm-6 col-md-4 col-lg-3"
			}) 
		</div>	

	</div>		
	
@i(elements/footer.php)