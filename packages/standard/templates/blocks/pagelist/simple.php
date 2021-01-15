<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<section class="uk-margin-top uk-margin-bottom">
	<@ foreach in pagelist ~@>
		<div class="uk-panel uk-panel-box" <@ ../../elements/colors_inline.php @>>
			<div class="uk-panel-title uk-margin-bottom-remove">
				<a href="@{ url }" class="nav-link">
					<@ ../../elements/icon.php @>
					@{ title }
				</a>
			</div>
			<@ ../../snippets/subtitle.php @>
			<@~ ../../elements/set_teaser_variable.php @>
			<p class="content uk-margin-bottom-remove">@{ :teaser }</p>
			<@ ../../elements/more.php @>
		</div>
	<@~ end @>
</section>
