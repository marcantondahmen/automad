<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<@ metaTitle { title: @{meta_title} } @>
	<@ jquery @>
	<@ bootstrap/js @>
	<@ bootstrap/css @>
	<link type="text/css" rel="stylesheet" href="/themes/@{theme}/css/standard.min.css" />
</head>


<body class="level-@{:level}">

	<@ bootstrap/navbar {
		brand: @{brand},
		fluid: false,
		fixedToTop: true,
		search: "Search",
		levels: 2
	} @>
