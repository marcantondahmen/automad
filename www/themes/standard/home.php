<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
	<meta content="utf-8" http-equiv="encoding">
	<title>$[siteName] / [title]</title>
	<meta name="app" content="Automad <?php echo VERSION; ?>">
	<link rel="stylesheet" type="text/css" href="$[themeURL]/style.css" />
</head>

<body>	
	$[img(file: /shared/MAD-Logo.png, height: 100, link: http://marcdahmen.de, target: _blank)]
	<br /><br />
	$[includeHome]
	$[searchField(Search me...)]
	$[navPerLevel]	
	<h1>[title]</h1>
	[text]
	$[menuFilterAll]
	$[menuSortType(Date, title: Title)]
	$[menuSortDirection]
	$[listAll(title,tags)]
	<br />
	<p>$[navBreadcrumbs]</p>
	<br />
	<p>&copy $[year] by <a href="/">$[siteData(owner)]</a></p>
	<br />
	<p>Made with Automad <?php echo VERSION; ?></p>
</body>
</html>