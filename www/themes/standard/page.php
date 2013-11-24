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
	t[includeHome]
	t[search]
	t[navTreeCurrent]
	t[navPerLevel(2)]
	<br />		
	t[linkPrev]
	t[linkNext]
	<br />
	<h1>p[title]</h1>
	<h2>p[subtitle]</h2>
	t[imgSet(file: images/*.jpg, width: 200, height: 200, crop: 1)]
	t[filterParentByTags]
	p[text]
	<br />
	<p>Related Pages:</p>

	t[listSetup(title, tags, type: related)]
	t[listPages]

	<br />
	t[navBreadcrumbs]
	<br />
	<p>Made with Automad <?php echo AM_VERSION; ?></p>
</body>
</html>