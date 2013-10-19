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
	<p>$[searchField(Search me...)]</p>
	<p>$[navTreeCurrent]</p>	
	<h1>$(title)</h1>
	<p>$(text)</p>
	
	$[sortOriginalOrder]
	$[sortBy(title)]
	$[sortAscending]
	
	$[listAllFilters]
	$[listAll(title,tags)]
	
	<br />
	<pre><?php print_r ($this->S->getCollection()); ?></pre>
	<br />
	<p>Made with Automad <?php echo VERSION; ?></p>
</body>
</html>