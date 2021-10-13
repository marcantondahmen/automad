<@ set { 
	:visibilityCache: @{ :hideThumbnails | def (0) },
	:hideThumbnails: @{ checkboxHideThumbnails } 
} @>
<div class="@{ :classes | def('cards cards-large clean masonry') }">
	<@ foreach in pagelist ~@>
		<div class="card" <@ ../../elements/colors_inline.php @>>
			<div class="card-content uk-panel uk-panel-box">
				<@ if not @{ :hideThumbnails } and not @{ iconPanel } @>
					<@~ ../../elements/set_image_card_variable.php @>
					<@ if @{ :imageCard } ~@>
						<div class="uk-panel-teaser">
							<a href="@{ url }"><img src="@{ :imageCard }"></a>
						</div>
					<@~ end ~@>
				<@ end @>
				<div class="panel-body">
					<div class="uk-panel-title">
						<a href="@{ url }">
							<@ ../../elements/icon.php @>
							@{ title }
						</a>
						<div class="text-subtitle">
							<@ ../../elements/date.php @>
							<@ if @{ date } and @{ tags } @><br><@ end @>
							<@ ../../elements/tags.php @>
						</div>
					</div>
					<@~ ../../elements/set_teaser_variable.php @>
					<p class="content uk-margin-bottom-remove">@{ :teaser }</p>
				</div>
				<@ ../../elements/more.php @>
			</div>
		</div>
	<@ else @>
		<div class="card">
			<div class="card-content uk-panel uk-panel-box">
				<div class="uk-panel-title uk-margin-remove">
					@{ notificationNoSearchResults | def ('No Pages Found') }
				</div>
			</div>
		</div>
	<@~ end @>
</div>
<@ set { 
	:hideThumbnails: @{ :visibilityCache } 
} @>