i{elements/header.php}

	<div class="top">

		<div class="sitename">
			<a href="/">s{sitename}</a>
		</div>

		t{includeHome}
		t{navTop}
		t{search(Search)}
		
		<h3 class="results">p{title}</h3>

		t{listSetup(title, subtitle, file: *.jpg, width: 250, height: 150, crop: 1)}
		t{listSortTypes}
		t{listSortDirection}
	
	</div>

	t{listPages}
	
i{elements/footer.php}