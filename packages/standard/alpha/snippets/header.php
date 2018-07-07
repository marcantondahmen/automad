<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@{ sitename } / @{ title | def('404') }</title>
	<@ ../../snippets/favicons.php @>
	<link href="https://fonts.googleapis.com/css?family=Rubik:400,400i,500,500i,700,700i,900,900i&amp;subset=cyrillic,latin-ext" rel="stylesheet">
	<link href="/packages/standard/alpha/dist/alpha.min.css" rel="stylesheet">
	<script src="/packages/standard/dist/standard.min.js"></script>
	<# Add optional header items. #>
	@{ itemsHeader }
</head>

<body class="@{ theme | sanitize } @{ :template | sanitize }">
	
	<@ ../../snippets/navbar.php @>
	
	<div class="uk-container uk-container-center navbar-push">
		<div class="uk-block">
					