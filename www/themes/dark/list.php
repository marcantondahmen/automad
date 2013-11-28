i{elements/header.php}

	<div class="top">
	
		<div class="sitename">
			<a href="/">s{sitename}</a>
		</div>
	
		t{includeHome}
		t{navPerLevel(1)}
		t{search(Search)}	
	
		<h1>p{title}</h1>
		
		<h2>p{subtitle}</h2>
		
		<div class="text">p{text}</div>

		t{listSetup(title, subtitle, tags, type: children, template: page, file: *.jpg, width: 250, height: 150, crop: 1)}
		t{listFilters}
		t{listSortTypes}
		t{listSortDirection(SORT_ASC: Ascending, SORT_DESC: Descending)}
		
	</div>
		
	t{listPages}

i{elements/footer.php}