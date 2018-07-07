<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ snippets/header.php @>

	<ul class="uk-grid uk-grid-width-small-1-2 <@ 
	with @{ imageProfile | def('*.jpg, *.jpeg, *.png, *.gif')} 
	@>masonry<@ 
	end @>">
		<li class="uk-block">
			<h1>@{ title }</h1>
		</li>
		<li class="uk-block">
			<@ with @{ imageProfile | def('*.jpg, *.jpeg, *.png, *.gif')} { width: 780 } @>
				<div class="uk-panel uk-panel-box">
					<div class="uk-panel-teaser">
						<img 
						src="@{ :fileResized }" 
						alt="@{ :basename }" 
						width="@{ :widthResized }" 
						height="@{ :heightResized }" 
						/>
					</div>
				</div>
			<@ end @>
		</li>
		<li>
			<div class="content uk-block">
				@{ textTeaser | markdown }
			</div>
			<div class="uk-block">
				<@ ../snippets/email_form.php @>
			</div>
		</li>
	</ul>

<@ snippets/footer.php @>