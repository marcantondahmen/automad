<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<# Reset variable to false in case there is no match. #>
<@ set { :imageTeaser: false } @>
<# Try to get image from variable. #>
<@ with @{ imageTeaser | def ('*.jpg, *.png, *.gif') } { width: 800 } ~@>
	<@ set { :imageTeaser: @{ :fileResized } } @>
<@~ else ~@>
	<# Else try to get first image from content. #>
	<@ set { :imageTeaser: 
		@{ +main | 
			def (@{ textTeaser | markdown }) | 
			def (@{ text | markdown }) |
			findFirstImage 
		}
	} @>
<@~ end @>