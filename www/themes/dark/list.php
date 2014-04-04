i(elements/header.php)

	<div class="top">
	
		t(navPerLevel {levels: 1, homepage: true})
		t(search {placeholder: "Search this site"})	
	
		<h1>p(title)</h1>
		
		<h2>p(subtitle)</h2>
		
		<div class="content">p(text)</div>

		t(listSetup {
			vars: "title, subtitle, tags", 
			type: "children", 
			glob: "*.jpg", 
			width: 250, 
			height: 150, 
			crop: 1
		})
		
		t(listFilters)
		t(listSortTypes {
			title: "Title", 
			subtitle: "By Subtitle", 
			tags: "By Tags"
		})
		
		t(listSortDirection {asc: "Asc", desc: "Desc"})
		
	</div>
		
	t(listPages)

i(elements/footer.php)