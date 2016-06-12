<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ elements/header.php @>

	<div class="container">

		<div class="row">
			<div id="title" class="col-md-6">
				<h1>@{title}</h1>
				<h2>@{subtitle}</h2>
			</div>
			<div class="col-md-6">
				@{text | markdown}
			</div>
		</div>

		<div class="row">
			<@ pagelist { type: "children" } @>
			<div class="col-md-6">
				<@ pagelistFilters @>
			</div>
			<div class="col-md-6">
				<@ pagelistSort {
					"Title": {
						sortItem: "title",
						sortOrder: "asc"
					},
					"Subtitle": {
						sortItem: "subtitle",
						sortOrder: "asc"
					}
				} @>
			</div>
		</div>

		<div class="row">
			<@ pagelistMarkup {
				variables: "title, subtitle",
				glob: "*.jpg, *.png",
				width: 350,
				height: 350,
				crop: true,
				class: "item image col-xs-6 col-sm-6 col-md-4 col-lg-3",
				firstWidth: 800,
				firstHeight: 800,
				firstClass: "item image col-xs-12 col-sm-12 col-md-8 col-lg-6"
			} @>
		</div>

	</div>

<@ elements/footer.php @>
