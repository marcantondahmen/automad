<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@~ if not @{ checkboxHidePrevNextNav } @>
	<@~ newPagelist { type: 'siblings' } @>
	<@~ if @{ :pagelistCount } @>
		<nav class="prev-next">
			<@ with prev ~@>
				<a href="@{ url }" class="nav-link prev" title="@{ title }" data-uk-tooltip="{pos:'right',animation:true}">
					<svg class="bi bi-arrow-left" width="1em" height="1em" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
  						<path fill-rule="evenodd" d="M5.854 4.646a.5.5 0 010 .708L3.207 8l2.647 2.646a.5.5 0 01-.708.708l-3-3a.5.5 0 010-.708l3-3a.5.5 0 01.708 0z" clip-rule="evenodd"/>
  						<path fill-rule="evenodd" d="M2.5 8a.5.5 0 01.5-.5h10.5a.5.5 0 010 1H3a.5.5 0 01-.5-.5z" clip-rule="evenodd"/>
					</svg>
				</a>
			<@~ end @>
			<@~ with next ~@>
				<a href="@{ url }" class="nav-link next" title="@{ title }" data-uk-tooltip="{pos:'left',animation:true}">
					<svg class="bi bi-arrow-right" width="1em" height="1em" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
  						<path fill-rule="evenodd" d="M10.146 4.646a.5.5 0 01.708 0l3 3a.5.5 0 010 .708l-3 3a.5.5 0 01-.708-.708L12.793 8l-2.647-2.646a.5.5 0 010-.708z" clip-rule="evenodd"/>
  						<path fill-rule="evenodd" d="M2 8a.5.5 0 01.5-.5H13a.5.5 0 010 1H2.5A.5.5 0 012 8z" clip-rule="evenodd"/>
					</svg>
				</a>
			<@~ end @>
		</nav>
	<@~ end @>
<@~ end @>