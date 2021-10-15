<@ foreach in pagelist @>
	<section class="am-block">
		<a href="@{ url }"><h2>@{ title }</h2></a>
		<p>
			<@ ../../elements/date.php @>
			<@ if @{ date } and @{ tags } @>&nbsp;&mdash;&nbsp;<br class="uk-visible-small"><@ end @>
			<@ ../../elements/tags.php @>
		</p>
		<@~ ../../elements/set_image_card_variable.php @>
		<@ if @{ :imageCard } ~@>
			<figure>
				<a href="@{ url }">
					<img class="image-full-width" src="@{ :imageCard }">
				</a>
			</figure>
		<@~ end ~@>
		<@~ ../../elements/set_teaser_variable.php @>
		<p class="content uk-margin-bottom-remove">@{ :teaser }</p>
		<div class="uk-margin-large-bottom"><@ ../../elements/more.php @></div>
	</section>
<@ end @>