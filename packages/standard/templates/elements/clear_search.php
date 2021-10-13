<@~ if @{ ?search } ~@>
	<a href="?<@ queryStringMerge { search: false } @>" class="uk-button">
		@{ labelClearSearch | def('Clear Search Results for') } "@{ ?search }"
	</a>
<@~ end ~@>