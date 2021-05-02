<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ if @{ tags } ~@>
	<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 28 30">
		<path d="M12,0H0v12l14,14l12-12L12,0z M3,10.757V3h7.757l11,11L14,21.758L3,10.757z"/>	
	</svg>&nbsp;
<@~ end @>
<@~ foreach in tags ~@>
	<@~ if @{ :i } > 1 @>,&nbsp;<@ end ~@>
	<a href="@{ urlTagLinkTarget | def(@{ :parent }) }?filter=@{ :tag }">@{ :tag }</a>
<@~ end ~@>
	