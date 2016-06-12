<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ elements/header.php @>

	<div class="jumbotron">

		<div class="container">
			<div class="row">
				<div class="col-md-6">
					@{jumbotron_1 | markdown}
				</div>
				<div class="col-md-6">
					@{jumbotron_2 | markdown}
				</div>
			</div>
		</div>

	</div>

	<div class="container">
		<div class="row">
			<div class="col-md-4">
				@{text | markdown}
			</div>
			<div class="col-md-4">
				@{text_2 | markdown}
			</div>
			<div class="col-md-4">
				@{text_3 | markdown}
			</div>
		</div>
	</div>

<@ elements/footer.php @>
