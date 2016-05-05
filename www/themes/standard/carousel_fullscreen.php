<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
{@ elements/header.php @}

	{@ bootstrap/carousel {
		files: {[ carousel_files ]},
		width: 1600,
		height: 900,
		fullscreen: true
	} @}
	
	<div class="container">
		
		<div class="row">
			<div id="title" class="col-md-12">
				<h1>{[ title ]}</h1>
				<h2>{[ subtitle ]}</h2>
			</div>
			<div class="col-md-12">
				{@ filterParentByTags @}
			</div>
			<div class="col-md-6">
				{[ text | markdown ]}
			</div>
			<div class="col-md-6">
				{[ text_2 | markdown ]}
			</div>
		</div>	
		
		<hr>
			
		<div class="row">
			{@ pagelist { type: "related" } @}
			{@ pagelistMarkup {
				variables: "title, subtitle",
				crop: true,
				class: "text-only col-xs-6 col-sm-6 col-md-4 col-lg-3"
			} @} 
		</div>		
			
	</div>		
	
{@ elements/footer.php @}