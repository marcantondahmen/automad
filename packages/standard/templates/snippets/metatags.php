<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ set_teaser_variable.php @>
<@ set { :description: @{ metaDescription | def(@{ :teaser | stripTags }) } } @>
<@ Standard/MetaTags { 
	description: @{ :description },
	ogTitle: @{ metaTitle | def('@{ sitename } / @{ title | def ("404") }') },
	ogDescription: @{ :description },
	ogType: 'website',
	ogImage: @{ ogImage | def('*.jpg, *.png, *.gif, /shared/*.jpg, /shared/*.png, /shared/*.gif') }
} @>