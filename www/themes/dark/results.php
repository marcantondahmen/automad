i{elements/header.php}

	<div class="top">

		t{includeHome}
		t{navTop}
		t{search(Search)}
		
		t{listSetup(title, subtitle, file: *.jpg, width: 250, height: 150, crop: 1)}
		
		<h3 class="results">p{title} (t{listCount})</h3>

		t{listSortTypes(By Name, subtitle: By Subtitle, tags: By Tags)}
		t{listSortDirection}
	
	</div>
	
	t{listPages}
	
i{elements/footer.php}