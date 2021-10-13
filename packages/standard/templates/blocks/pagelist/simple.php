<@ set { 
	:visibilityCache: @{ :hideThumbnails | def (0) },
	:hideThumbnails: @{ checkboxHideThumbnails } 
} @>
<section class="cards-simple">
	<@ foreach in pagelist ~@>
		<div class="card uk-panel uk-panel-box" <@ ../../elements/colors_inline.php @>>
			<div class="uk-flex">
				<div class="uk-width-3-4 uk-flex-item-1">
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
				<@ if not @{ :hideThumbnails } and not @{ iconPanel } @>
					<@~ ../../elements/set_image_card_variable.php @>
					<@~ if @{ :imageCard } @>
						<div class="uk-panel-teaser uk-width-1-4 uk-hidden-small">
							<a href="@{ url }"><img src="@{ :imageCard }"></a>
						</div>
					<@~ end ~@>
				<@ end @>
			</div>
		</div>
	<@~ end @>
</section>
<@ set { 
	:hideThumbnails: @{ :visibilityCache } 
} @>