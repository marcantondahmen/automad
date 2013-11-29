i{elements/header.php}

	<div class="top">

		<div class="sitename">
			<a href="/">s{sitename}</a>
		</div>

		t{includeHome}
		t{navTop}
		t{search(Search)}
		
		<h1>p{title}</h1>
		
		<h2>p{subtitle}</h2>

		<div class="text">p{text}</div>

		t{listSetup(title, subtitle, tags, template: project, file: *.jpg, width: 250, height: 150, crop: 1)}
		t{listFilters}
		t{listSortTypes(By Name, subtitle: By Subtitle, tags: By Tags)}
		t{listSortDirection}
	
	</div>

	t{listPages}
	
i{elements/footer.php}