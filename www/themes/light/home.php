i(elements/header.php)

	<div class="top">

		t(navTop {homepage: true})
		t(search {placeholder: "Search this site!"})		
		
		<h1>p(title)</h1>
		
		<h2>p(subtitle)</h2>

		<div class="content">p(text)</div>

		t(listSetup {
			vars: "title, subtitle, tags", 
			template: "project", 
			glob: "*.jpg", 
			width: 250, 
			height: 150, 
			crop: 1
		})
		
		t(listFilters)
		
		t(listSortItems {
			title: "By Name", 
			subtitle: "By Subtitle", 
			tags: "By Tags"
		})
		
		t(listSortOrder {asc: "Ascending", desc: "Descending"})
	
	</div>

	t(listPages)
	
i(elements/footer.php)