<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
{@ elements/header.php @}

	<div class="container">
		
		<div class="row">
			<div class="col-md-12 prev-next">	
				{@ linkPrev @}
				<div class="pull-right">{@ linkNext @}</div>		
			</div>
			<div class="col-md-12">
				{@ carousel {
					files: {[ carousel_files ]},
					width: 1200,
					height: 675
				} @}
			</div>
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