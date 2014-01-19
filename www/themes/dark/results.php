i(elements/header.php)

	<div class="top">

		t(includeHome)
		t(navTop)
		t(search)
		
		t(listSetup {
			vars: "title, subtitle", 
			glob: "*.jpg", 
			width: 250, 
			height: 150, 
			crop: 1
		})
		
		<h3 class="results">p(title) (t(listCount))</h3>
		
		t(listFilters)
		
		t(listSortTypes {
			title: "By Name", 
			subtitle: "By Subtitle", 
			tags: "By Tags"
		})
		
		t(listSortDirection)
	
	</div>
	
	t(listPages)
	
i(elements/footer.php)