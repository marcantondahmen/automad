<div class="@{ :classes | def('cards masonry clean') }">
	<@ foreach in pagelist ~@>
		<@~ ../../elements/set_image_card_variable.php @>
		<@~ if @{ :imageCard } @>
			<a href="@{ url }" class="card">
				<div class="card-content panel-image">
					<img src="@{ :imageCard }">
				</div>
			</a>
		<@ end @>
	<@~ end @>
</div>