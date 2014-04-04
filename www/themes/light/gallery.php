i(elements/header.php)

	<div class="top">
		
		t(navTop {homepage: true})
		t(search)
		
		<h1>p(title)</h1>
		
		<h2>p(subtitle)</h2>		

	</div>

	<div class="content">		
		
		x(Gallery {
			glob: "/pages/*/*/*.jpg", 
			width: 250, 
			height: 250
		})
		
	</div>
	
i(elements/footer.php)