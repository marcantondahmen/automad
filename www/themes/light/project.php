i(elements/header.php)

	<div class="top">
		
		t(navPerLevel {levels: 2, homepage: true})
		t(search {placeholder: "Search this site"})
		
		<div class="neighbors">
			t(linkPrev {text: "<"}) 
			t(linkNext {text: ">"})
		</div>
		
		<h1>p(title)</h1>
		
		<h2>p(subtitle)</h2>
	
		t(filterParentByTags)		
		
		<div class="images">
			x(Slider {
				glob: "*.jpg", 
				width: 850, 
				height: 450, 
				duration: 3000
			}) 
		</div>
			
	</div>

	<div class="container">

		<div class="content left">		
			p(text)	
		</div>
	
		<div class="content right">
			t(navSiblings)
		</div>
	
	</div>
	
	t(listSetup {
		vars: "title, subtitle, tags", 
		type: "related", 
		glob: "*.jpg", 
		width: 250, 
		height: 150, 
		crop: true
	})
	
	t(listPages)
	
i(elements/footer.php)