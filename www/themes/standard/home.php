<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
	<meta content="utf-8" http-equiv="encoding">
	<title>t[siteName] / p[title]</title>
	<meta name="app" content="Automad <?php echo AM_VERSION; ?>">
	<link rel="stylesheet" type="text/css" href="t[themeURL]/style.css" />
</head>

<body>	
	t[img(file: /shared/MAD-Logo.png, height: 100, link: http://marcdahmen.de, target: _blank)]
	<br /><br />
	t[includeHome]
	t[searchField(Search me...)]
	t[navPerLevel]	
	<h1>p[title]</h1>
	p[text]
	t[menuFilterAll]
	t[menuSortType(Date, title: Title)]
	t[menuSortDirection]
	t[listAll(title,tags)]
	<br />
	<p>t[navBreadcrumbs]</p>
	<br />
	<p>&copy t[year] by <a href="/">t[siteData(owner)]</a></p>
	<br />
	<p>Made with Automad <?php echo AM_VERSION; ?></p>
</body>
</html>