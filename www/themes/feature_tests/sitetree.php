<@ elements/header.php @>

	<# Snippets ------------------------------- #>
	<@ snippet item @>
		<a class="level-@{:level} list-group-item<@ if @{:current-path} @> list-group-item-info<@ end @><@ if @{:current} @> active<@ end @>" href="@{url}">
			@{title}
			<@ if @{:pagelist-count} @><span class="badge">@{:pagelist-count}</span><@ end @>
		</a>
		<@ if @{:pagelist-count} @>
		<div class="list-group-item<@ if @{:current-path} @> list-group-item-info<@ end @>">
			<div class="list-group">
				<@ foreach in pagelist @>
					<@ item @>
				<@ end @>
			</div>
		</div>
		<@ end @>
	<@ end @>
	<@ snippet column @>
		<@ pagelist { type: "siblings" } @>
		<div class="col-md-3">
			<div class="list-group">
			<@ foreach in pagelist @>
			<@ pagelist { type: "children" } @>
			<a class="level-@{:level} list-group-item<@ if @{:current-path} @> current-path list-group-item-info<@ end @><@ if @{:current} @> current active<@ end @>" href="@{url}">
				<span class="glyphicon glyphicon-folder-<@ if @{:current-path} @>open<@ else @>close<@ end @>" aria-hidden="true"></span>&nbsp;
				@{title}
				<@ if @{:pagelist-count} @><span class="badge">@{:pagelist-count}</span><@ end @>
			</a>
			<@ end @>
			</div>
		</div>
		<@ pagelist { type: "children" } @>
		<@ foreach in pagelist @>
			<@ if @{:current-path} @>
				<@ column @>
			<@ end @>
		<@ end @>
	<@ end @>
	<# ---------------------------------------- #>

	<div class="row">
		<div class="col-md-12">
			<h1>@{title}</h1>
			<hr>
		</div>
		<div class="col-md-3"><h4>Nested Sitetree</h4></div>
		<div class="col-md-9"><h4>Sitetree with Columns</h4></div>
		<div class="col-md-3">
			<div class="list-group">
				<@ pagelist { type: "children", parent: false, sortOrder: "asc" } @>
				<@ with "/" @>
					<@ item @>
				<@ end @>
			</div>
		</div>
		<@ with "/" @>
			<@ column @>
		<@ end @>
	</div>

<@ elements/footer.php @>
