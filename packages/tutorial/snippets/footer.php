<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
		<section id="source" class="section">
			<hr />
			<div class="tags has-addons">
				<span class="tag is-dark">Source</span>
				<span class="tag is-light">/packages/@{ theme }/@{ :template }.php</span>
			</div>
			<div class="content">
				<@ tutorial/source { 
					file: '/packages/@{ theme }/@{ :template }.php' 
				} @>
			</div>
		</section>
		<section class="section">	
			<nav class="breadcrumb" aria-label="breadcrumbs">
				<ul>
					<@ newPagelist { 
						type: 'breadcrumbs',
						excludeHidden: false
					} @>
					<@ foreach in pagelist @>
						<li><a href="@{ url }">@{ title }</a></li>
					<@ end @>
				</ul>
			</nav>
		</section>
		<section class="section">
			Made with <a href="https://automad.org">Automad</a>
			<br />
			Released under the <a href="https://automad.org/license">MIT license</a>
			<br />
			&copy; @{ :now | dateFormat('Y') } by
			<a href="https://marcdahmen.de">Marc Anton Dahmen</a>
		</section>
	</div>
</body>
</html>