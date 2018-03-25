<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@{ sitename } / @{ title }</title>
	<link href="https://fonts.googleapis.com/css?family=Rubik:400,400i,500,500i,700,700i,900,900i&amp;subset=cyrillic,latin-ext" rel="stylesheet">
	<link href="/packages/am/theme_02/dist/theme_02.min.css" rel="stylesheet">
	<script src="/packages/am/dist/am.min.js"></script>
	<# Add optional header items. #>
	@{ itemsHeader }
</head>

<body class="am-02-@{ :template | sanitize }">

	<div class="uk-container uk-container-center">
		
		<@ navbar.php @>	