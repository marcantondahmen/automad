<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<# Create snippet to be used recursively #>
<@ snippet tree @>
	<# Only show children/siblings in current path #>
	<@~ if @{ :currentPath } @>
		<# Only create new list in case the current context has children #>
		<@~ if @{ :pagelistCount } @>
			<ul class="uk-nav uk-nav-side">
				<@~ foreach in pagelist @>
					<@~ if not @{ checkboxHideInMenu } ~@>
						<li<@ if @{ :current } @> class="uk-active"<@ end @>>
							<a href="@{ url }">@{ title | stripTags }</a>
							<# Call tree snippet recursively #>
							<@~ tree ~@>
						</li>
					<@~ end @>
				<@~ end @>
			</ul>
		<@~ end @>
	<@~ end @>
<@~ end ~@>
<# Create new pagelist including all children adapting to the current context. #>
<@~ newPagelist { 
	type: 'children',
	excludeHidden: false 
} ~@>
<# Change context to the homepage #>
<@~ with "/" @>
	<@~ if not @{ checkboxHideInMenu } ~@>
		<ul class="uk-nav uk-nav-side">
			<li<@ if @{ :current } @> class="uk-active"<@ end @>>
				<a href="@{ url }">@{ title }</a>
			</li>
		</ul>
	<@~ end @>
	<# Call recursive tree snippet #>
	<@~ tree @>	
<@~ end @>