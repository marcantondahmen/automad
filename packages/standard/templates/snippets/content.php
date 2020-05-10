<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ title.php @>
<@ if not @{ +main } and @{ textTeaser | def (@{ text }) } @>
	@{ textTeaser | markdown }
	<@~ filelist { 
		glob: @{ imagesSlideshow | def('*.jpg, *.jpeg, *.png, *.gif') }, 
		sort: 'asc' 
	} ~@>
	<@ if @{ :filelistCount } ~@>
		<figure class="am-stretched">
			<@ slideshow.php @>
		</figure>
	<@~ end @>
	@{ text | markdown }
<@ else @>
	@{ +main }
<@ end @>