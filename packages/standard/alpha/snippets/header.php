<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<@ set { :version: '<?php echo AM_VERSION; ?>' } @>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@{ metaTitle | def('@{ sitename } / @{ title | def ("404") }') }</title>
	<@ ../../snippets/metatags.php @>
	<@ ../../snippets/favicons.php @>
	<# 
	
	To make sure the following variables are always available in the dashboard, 
	they can be included in a comment block.
	
	@{ imageTeaser } 
	
	#>
	<link href="https://fonts.googleapis.com/css2?family=Fira+Mono&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&display=swap" rel="stylesheet">	
	<link href="/packages/standard/alpha/dist/alpha.min.css?v=@{ :version | sanitize }" rel="stylesheet">
	<script src="/packages/standard/dist/standard.min.js?v=@{ :version | sanitize }"></script>
	<# Add optional header items. #>
	@{ itemsHeader }
</head>

<body class="@{ theme | sanitize } @{ :template | sanitize }">
	<@ ../../snippets/navbar.php @>
	<div class="uk-container uk-container-center navbar-push">