<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ set { :hideThumbnails: @{ checkboxHideThumbnails } } @>
<section class="cards-simple">
	<@ foreach in pagelist ~@>
		<div class="card uk-panel uk-panel-box" <@ ../../elements/colors_inline.php @>>
			<div class="uk-flex">
				<div>
					<div class="uk-panel-title uk-margin-bottom-remove">
						<a href="@{ url }">
							<@ ../../elements/icon.php @>
							@{ title }
						</a>
					</div>
					<@ ../../snippets/subtitle.php @>
					<@~ ../../elements/set_teaser_variable.php @>
					<p class="content uk-margin-bottom-remove">@{ :teaser }</p>
					<@ ../../elements/more.php @>
				</div>
				<@ if not @{ :hideThumbnails } and not @{ pageIconSvg } @>
					<@~ ../../elements/set_imageteaser_variable.php @>
					<@~ if @{ :imageCard } @>
						<div class="uk-panel-teaser">
							<a href="@{ url }"><img src="@{ :imageCard }"></a>
						</div>
					<@~ end ~@>
				<@ end @>
			</div>
		</div>
	<@~ end @>
</section>
