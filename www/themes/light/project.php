i{elements/header.php}

	<div class="top">
		
		<div class="sitename">
			<a href="/">s{sitename}</a>
		</div>
		
		t{includeHome}
		t{navTop}
		t{search(Search)}
		
		<div class="neighbors">
			t{linkPrev(<)}
			t{linkNext(>)}
		</div>
		
		<h1>p{title}</h1>
		
		<h2>p{subtitle}</h2>
	
		t{filterParentByTags}		
	
		<div class="images">
			t{img(file: *.jpg, width: 850, height: 450, crop: 1)}
		</div>

	</div>

	<div class="container">

		<div class="text left">		
			p{text}	
		</div>
	
		<div class="text right">
			t{navSiblings}
		</div>
	
	</div>
	
	t{listSetup(title, subtitle, tags, type: related, file: *.jpg, width: 250, height: 150, crop: 1)}
	t{listPages}
	
i{elements/footer.php}