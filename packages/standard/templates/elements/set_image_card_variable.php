<# Reset variable to false in case there is no match. #>
<@ set { :imageCard: false } @>
<# Try to get image from variable. #>
<@ with @{ imageCard | def ('*.jpg, *.png, *.gif') } { width: 800 } ~@>
	<@ set { :imageCard: @{ :fileResized } } @>
<@~ else ~@>
	<# Else try to get first image from content. #>
	<@ set { :imageCard: 
		@{ +main | 
			def (@{ textTeaser | markdown }) | 
			def (@{ text | markdown }) |
			findFirstImage 
		}
	} @>
<@~ end @>