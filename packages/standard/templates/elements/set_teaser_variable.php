<# Reset variable to false in case there is no match. #>
<@~ set { :teaser: false } @>
<# Try to get first paragraph from content. #>
<@~ set { :teaser: 
	@{ +hero |
		findFirstParagraph |
		def (@{
			+main |
			def (@{ textTeaser | markdown }) | 
			def (@{ text | markdown }) |
			findFirstParagraph
		}) |
		stripTags
	}
} @>