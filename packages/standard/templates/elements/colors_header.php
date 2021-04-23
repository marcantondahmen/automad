<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>	
<@~ if @{ :c | def('@{colorPageText}@{colorPageBackground}@{colorPageBorder}@{colorCardText}@{colorCardBackground}@{colorCardBorder}@{colorCodeBackground}@{colorNavbarText}@{colorNavbarBackground}@{colorNavbarBorder}') } ~@>
	<style>:root.@{ theme | sanitize } {<@ colors.php @>}</style>
<@~ end @>