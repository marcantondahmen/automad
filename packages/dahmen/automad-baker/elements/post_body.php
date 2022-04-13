<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ navbar.php @>
<@ title.php @>
<# Content. #>
<div class="baker-content">
	@{ +main }	
</div>
<# Children nav for small devices. #>
<@ newPagelist { type: 'children' } @>
<@ if @{ :pagelistCount } @>
	<div class="uk-hidden-large">
		<hr />
		<h2 class="uk-margin-top uk-margin-bottom">
			More in @{ title }
		</h2>
		<ul class="baker-nav baker-nav-large">
			<@ foreach in pagelist @>
				<li><a href="@{ url }">@{ title }</a></li>
			<@ end @>
		</ul>
	</div>
<@ end @>
<# Related pages. #>
<@ if not @{ checkboxHideRelatedPages } @>
	<@ newPagelist { 
		excludeHidden: false,
		type: 'related',
		sort: ':path asc'
	} @>
	<@ if @{ :pagelistCount } @>
		<div class="baker-content">
			<@ if not @{ checkboxSimpleRelatedPagelist } @>
				<@ masonry_config.php @>
				<@ ../blocks/pagelist/masonry.php @>
			<@ else @>
				<hr>
				<@ ../blocks/pagelist/simple.php @>
			<@ end @>
		</div>
	<@ end @>
<@ end @>
<@ footer_nav.php @>