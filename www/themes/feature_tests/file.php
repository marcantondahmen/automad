{@ elements/header.php @}

	<div class="row">
		<div class="col-md-12">
			<h1>{[ title ]}</h1>
		</div>
		<div class="col-md-12">
			{@ with {[ test-file ]} @}
			{@ img { file: {[ :file ]}, width: 1200, class: "img-responsive img-rounded" } @}
			<br>
			{[ :caption | markdown ]}
			<p>{[ :basename ]}</p>
			{@ end @}
		</div>
	</div>	
	
{@ elements/footer.php @}