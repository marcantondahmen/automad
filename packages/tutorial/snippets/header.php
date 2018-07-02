<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@{ sitename } / @{ title }</title>
	<@ with @{ favicon } @><link href="@{ :file }" rel="shortcut icon" type="image/x-icon" /><@ end @>
	<@ with @{ appleTouchIcon } @><link href="@{ :file }" rel="apple-touch-icon" /><@ end @>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.0/css/bulma.min.css">
	<script defer src="https://use.fontawesome.com/releases/v5.0.7/js/all.js"></script>
</head>

<body>
	<div class="container">