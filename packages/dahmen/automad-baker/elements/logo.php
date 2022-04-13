<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<a href="/" class="brand">
	<@~ with @{ imageLogo } ~@>
		<img 
		src="<@ with @{ :file } { height: @{ logoHeight | def (40) } } @>@{ :fileResized }<@ end @>" 
		srcset="<@ with @{ :file } { height: @{ logoHeight | def (40) | *2 } } @>@{ :fileResized } 2x<@ end @>"
		alt="@{ :basename }"
		>
	<@~ else ~@>
		@{ brand | def (@{ sitename }) }
	<@~ end ~@>
</a>