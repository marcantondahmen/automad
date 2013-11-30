i{elements/header.php}

	<div class="top">
	
		t{includeHome}
		t{navPerLevel(1)}
		t{search(Search)}	
	
		<h1>p{title}</h1>
		
		<h2>p{subtitle}</h2>
		
		<div class="content">p{text}</div>

		t{listSetup(title, subtitle, tags, type: children, template: project, file: *.jpg, width: 250, height: 150, crop: 1)}
		t{listFilters}
		t{listSortTypes(By Name, subtitle: By Subtitle, tags: By Tags)}
		t{listSortDirection(SORT_ASC: Ascending, SORT_DESC: Descending)}
		
	</div>
		
	t{listPages}

i{elements/footer.php}