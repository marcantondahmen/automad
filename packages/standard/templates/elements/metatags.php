<@ set_teaser_variable.php @>
<@ set { :description: @{ metaDescription | def(@{ :teaser | stripTags }) } } @>
<@ Standard/MetaTags { 
	description: @{ :description },
	ogTitle: @{ metaTitle | def('@{ sitename } / @{ title | def ("404") }') },
	ogDescription: @{ :description },
	ogType: 'website',
	ogImage: @{ 
		ogImage | 
		def('@{ +hero | findFirstImage }') | 
		def('@{ +main | findFirstImage }') | 
		def('*.jpg, *.png, *.gif, /shared/*.jpg, /shared/*.png, /shared/*.gif') 
	}
} @>