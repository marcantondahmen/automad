<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

		<section class="section">	
			<nav class="breadcrumb" aria-label="breadcrumbs">
				<ul>
					<@ newPagelist { type: 'breadcrumbs' } @>
					<@ foreach in pagelist @>
						<li><a href="@{ url }">@{ title }</a></li>
					<@ end @>
				</ul>
			</nav>
		</section>


	</div>
</body>
</html>