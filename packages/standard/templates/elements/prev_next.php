<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@~ if not @{ checkboxHidePrevNextNav } @>
	<@~ newPagelist { type: 'siblings' } @>
	<@~ if @{ :pagelistCount } @>
		<nav class="am-block prev-next uk-margin-large-bottom">
			<@ with prev ~@>
				<a href="@{ url }" class="nav-link prev">
					<span class="arrow">⟵</span>
					<span class="uk-hidden-small uk-text-truncate">@{ title }</span>
				</a>
			<@~ end @>
			<@~ with next ~@>
				<a href="@{ url }" class="nav-link next">
					<span class="uk-hidden-small uk-text-truncate">@{ title }</span>
					<span class="arrow">⟶</span>
				</a>
			<@~ end @>
		</nav>
	<@~ end @>
<@~ end @>