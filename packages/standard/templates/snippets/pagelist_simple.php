<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ foreach in pagelist ~@>
	<div class="uk-panel uk-panel-box" <@ colors_inline.php @>>
		<div class="uk-panel-title uk-margin-bottom-remove">
			<a href="@{ url }" class="nav-link">@{ title }</a>
		</div>
		<@ subtitle.php @>
		<@~ set_teaser_variable.php @>
		<p class="content uk-margin-bottom-remove">@{ :teaser }</p>
		<@ more.php @>
	</div>
<@~ end @>
