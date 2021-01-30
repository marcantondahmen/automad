<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@~ if not @{ checkboxHidePrevNextNav } @>
	<@~ newPagelist { type: 'siblings' } @>
	<@~ if @{ :pagelistCount } @>
		<nav class="prev-next">
			<@ with prev ~@>
				<a href="@{ url }" class="nav-link prev" title="@{ title }" data-uk-tooltip="{pos:'right',animation:true}">
					⟵
				</a>
			<@~ end @>
			<@~ with next ~@>
				<a href="@{ url }" class="nav-link next" title="@{ title }" data-uk-tooltip="{pos:'left',animation:true}">
					⟶
				</a>
			<@~ end @>
		</nav>
	<@~ end @>
<@~ end @>