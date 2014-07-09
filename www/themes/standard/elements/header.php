<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	@t(metaTitle { title: @p(meta_title) })
	@t(jquery)
	@t(bootstrapJS)
	@t(bootstrapCSS)
	<link type="text/css" rel="stylesheet" href="@t(themeURL)/css/standard.min.css" />
</head>


<body class="level-@t(level)">
	
	@x(Navbar {
		brand: @s(brand),
		fluid: false,
		fixedToTop: true,
		search: "Search",
		levels: 2
	})