<@ elements/header.php @>

	<div class="row">
		<div class="col-md-12">
			<h1>@{title}</h1>
		</div>
		<@ pagelist { type: "children", parent: "/example-pages" } @>
		<@ foreach in pagelist @>
		<@ if @{:i} <= 2 @>
		<div class="col-sm-6 col-md-6">
			<hr>
			<h2>@{title}</h2>
			<@ img { file: "*.jpg", width: 600, class: "img-responsive img-rounded" } @>
			<br>
			<span class="label label-primary pull-right">@{:i} of @{:pagelist-count}</span>
			<a class="btn btn-lg btn-default" href="@{url}"><span class="glyphicon glyphicon-heart-empty" aria-hidden="true"></span> Go to page ...</a>
			<hr>
		</div>
		<@ else @>
		<div class="col-sm-4 col-md-4">
			<hr>
			<h3>@{title}</h3>
			<@ img { file: "*.jpg", width: 400, class: "img-responsive img-rounded" } @>
			<br>
			<span class="label label-primary pull-right">@{:i} of @{:pagelist-count}</span>
			<a class="btn btn-default" href="@{url}"><span class="glyphicon glyphicon-heart-empty" aria-hidden="true"></span> Go to page ...</a>
			<hr>
		</div>
		<@ end @>
		<@ end @>
	</div>

<@ elements/footer.php @>
