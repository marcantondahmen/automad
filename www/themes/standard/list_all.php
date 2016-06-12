<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ elements/header.php @>

	<div class="container">

		<div class="row">
			<div id="title" class="col-md-12">
				<h1>@{title}</h1>
				<h2>@{subtitle}</h2>
			</div>
			<div class="col-md-8">
				@{text | markdown}
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<@ pagelistFilters @>
				<@ pagelistSort {
					"Ascending": {
						sortItem: "title",
						sortOrder: "asc"
					},
					"Descending": {
						sortItem: "title",
						sortOrder: "desc"
					}
				} @>
			</div>
		</div>

		<div class="row">
			<@ pagelistMarkup {
				variables: "title, subtitle, text",
				class: "item text-only col-xs-12 col-sm-10 col-md-8"
			} @>
		</div>

	</div>

<@ elements/footer.php @>
