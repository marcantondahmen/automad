{@ elements/header.php @}

	<div class="row">
		<div class="col-md-12">
			<h1>{[ title ]}</h1>
			<hr>
		</div>
		<div class="col-md-3"><h4>Nested Sitetree</h4></div>
		<div class="col-md-9"><h4>Sitetree with Columns</h4></div>
		<div class="col-md-3">
			<div class="list-group">
				{@ with "/" @}
				{@ elements/sitetree_item.php @}
				{@ end @}
			</div>
		</div>
		{@ with "/" @}
		{@ elements/sitetree_column.php @}
		{@ end @}	
	</div>

{@ elements/footer.php @}