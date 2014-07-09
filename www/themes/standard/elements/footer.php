<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

	<div class="container">
		
		<div class="row">
			<div class="col-md-12">@t(navBreadcrumbs { separator: "<span class=\"glyphicon glyphicon-chevron-right\"></span>" })</div>
		</div>
			
		<div class="row">
			<div class="col-md-3"><span class="glyphicon glyphicon-copyright-mark"></span> @t(year) by <a href="/">@s(sitename)</a></div>
			<div class="col-md-3"><span class="glyphicon glyphicon-envelope"></span> @s(email)</div>
			<div class="col-md-3"><span class="glyphicon glyphicon-star"></span> <a href="http://automad.org" target="_blank">Made with Automad</a></div>
		</div>
	
	</div>

</body>
</html>