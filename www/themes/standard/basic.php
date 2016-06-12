<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ elements/header.php @>

	<div class="container">

		<div class="row">
			<div id="title" class="col-md-12">
				<h1>@{title}</h1>
				<h2>@{subtitle}</h2>
			</div>
			<div class="col-md-6">
				@{text | markdown}
			</div>
			<div class="col-md-6">
				@{text_2 | markdown}
			</div>
		</div>

	</div>

<@ elements/footer.php @>
