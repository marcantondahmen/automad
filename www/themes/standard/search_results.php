<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
{@ elements/header.php @}

	<div class="container">
		
		<div class="row">
			<div id="title" class="col-md-6">
				<h1>{[ title ]} ({[ :pagelist-count ]})</h1>
			</div>		
		</div>			
		<div class="row">
			<div class="col-md-12">
				{@ pagelistFilters @}
			</div>
			{@ pagelistMarkup {
				variables: "title, subtitle, text",
				class: "item text-only col-xs-12 col-sm-10 col-md-8"
			} @} 
		</div>	
		
	</div>

{@ elements/footer.php @}