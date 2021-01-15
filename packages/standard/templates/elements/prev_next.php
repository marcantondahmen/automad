<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@~ if not @{ checkboxHidePrevNextNav } @>
	<@~ newPagelist { type: 'siblings' } @>
	<@~ if @{ :pagelistCount } @>
		<nav class="prev-next">
			<@ with prev ~@>
				<a href="@{ url }" class="nav-link prev" title="@{ title }" data-uk-tooltip="{pos:'right',animation:true}">
					<svg xmlns="http://www.w3.org/2000/svg" width="1.7em" height="1.2em" viewBox="0 0 90 60">
						<polygon points="90,25 20.899,25 37.918,7.917 30,0 0,30 30,60 37.918,52.082 20.899,35 90,35 "/>
					</svg>
				</a>
			<@~ end @>
			<@~ with next ~@>
				<a href="@{ url }" class="nav-link next" title="@{ title }" data-uk-tooltip="{pos:'left',animation:true}">
					<svg xmlns="http://www.w3.org/2000/svg" width="1.7em" height="1.2em" viewBox="0 0 90 60">
						<polygon points="0,25 69.101,25 52.082,7.917 60,0 90,30 60,60 52.082,52.082 69.101,35 0,35 "/>
					</svg>
				</a>
			<@~ end @>
		</nav>
	<@~ end @>
<@~ end @>