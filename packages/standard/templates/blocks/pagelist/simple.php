<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<section class="uk-margin-top uk-margin-bottom">
	<@ foreach in pagelist ~@>
		<div class="uk-panel uk-panel-box" <@ ../../snippets/colors_inline.php @>>
			<div class="uk-panel-title uk-margin-bottom-remove">
				<a href="@{ url }" class="nav-link">
					<@ ../../snippets/icon.php @>
					@{ title }
				</a>
			</div>
			<@ ../../snippets/subtitle.php @>
			<@~ ../../snippets/set_teaser_variable.php @>
			<p class="content uk-margin-bottom-remove">@{ :teaser }</p>
			<@ ../../snippets/more.php @>
		</div>
	<@~ end @>
</section>
