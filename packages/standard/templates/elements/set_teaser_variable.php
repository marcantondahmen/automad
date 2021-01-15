<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<# Reset variable to false in case there is no match. #>
<@~ set { :teaser: false } @>
<# Try to get first paragraph from content. #>
<@~ set { :teaser: 
	@{ +main | 
		def (@{ textTeaser | markdown }) | 
		def (@{ text | markdown }) |
		findFirstParagraph 
	}
} @>