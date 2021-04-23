<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<!DOCTYPE html>
<html lang="en" class="@{ theme | sanitize }">
<head>
	<@ set { :version: '<?php echo AM_VERSION; ?>' } @>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@{ metaTitle | def('@{ sitename } / @{ title | def ("404") }') }</title>
	<@ metatags.php @>
	<@ favicons.php @>
	<# 
	
	To make sure the following variables are always available in the dashboard, 
	they can be included in a comment block.
	
	@{ imageCard } 
	@{ checkboxHideThumbnails }
	
	#>
	<link href="/packages/standard/dist/standard.min.css?v=@{ :version | sanitize }" rel="stylesheet">
	<script src="/packages/standard/dist/standard.min.js?v=@{ :version | sanitize }"></script>
	<@ colors_header.php @>
	<# Add optional header items. #>
	@{ itemsHeader }
</head>

<body class="@{ :template | sanitize }">
	<@ navbar.php @>
	@{ +hero | replace ('/^(.+)$/is', '<section class="hero content">$1</section>') }
	<div class="uk-container uk-container-center">