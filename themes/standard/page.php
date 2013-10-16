<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>$[siteName] / $(title)</title>
	<meta name="app" content="Automad <?php echo VERSION; ?>">
	<link rel="stylesheet" type="text/css" href="$[themeURL]/style.css" />
</head>

<body>
	<p>$[navTop]</p>
	<p>$[navChildren]</p>
	<p>$[navSiblings]</p>	
	<h1>$(title)</h1>
	<h2>$(subtitle)</h2>
	<h3>$(tags)</h3>
	<p>$(text)</p>
	<br />
	<p>Related Pages:</p>
	<p>$[relatedPages(title, tags)]</p>
	<br />
	<p>$[navBreadcrumbs]</p>
	<br />
	<p>Made with Automad <?php echo VERSION; ?></p>
</body>
</html>