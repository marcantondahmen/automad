<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
	<meta content="utf-8" http-equiv="encoding">
	<title>$[siteName] / $(title)</title>
	<meta name="app" content="Automad <?php echo VERSION; ?>">
	<link rel="stylesheet" type="text/css" href="$[themeURL]/style.css" />
</head>

<body>
	$[includeHome]
	
	<p>$[searchField(Search me...)]</p>
	<p>$[_navTreeCurrent]</p>
	<p>$[navPerLevel]</p>	
	<h1>$(title)</h1>
	<p>$(text)</p>
	
	$[menuFilterAll]
	$[menuSortType(Date, title: Title)]
	$[menuSortDirection]
	
	$[listAll(title,tags)]
	
	<br />
	<p>$[navBreadcrumbs]</p>
	<br />
	<pre><?php /* print_r ($this->S->getCollection()); */ ?></pre>
	<br />
	<p>Made with Automad <?php echo VERSION; ?></p>
</body>
</html>