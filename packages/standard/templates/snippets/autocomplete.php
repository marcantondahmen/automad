<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@~ newPagelist ~@>
[
<@~ foreach in pagelist ~@>
	<# Add comma before all elements except the first one. #>
	<@ if @{ :i | -1 } @>,<@ end @>
	<# Exclude redirected pages. #>
	<@~ if @{ :origUrl } = @{ url } ~@>
		{"value":"@{ title | escape }"}
	<@~ else ~@>
		<# Add empty array for redirected elements to make sure there is always an element following a comma. #>
		[]
	<@~ end @>
<@~ end ~@>
]