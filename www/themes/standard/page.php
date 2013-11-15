<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
	<meta content="utf-8" http-equiv="encoding">
	<title>$[siteName] / [title]</title>
	<meta name="app" content="Automad <?php echo AM_VERSION; ?>">
	<link rel="stylesheet" type="text/css" href="$[themeURL]/style.css" />
</head>

<body>
	$[includeHome]
	$[searchField()]
	$[navTreeCurrent]
	$[navPerLevel(2)]
	<br />		
	$[linkPrev]
	$[linkNext]
	<br />
	<h1>[title]</h1>
	<h2>[subtitle]</h2>
	$[filterParentByTags]
	[text]
	<br />
	<p>Related Pages:</p>
	$[relatedPages(title, tags)]
	<br />
	$[navBreadcrumbs]
	<br />
	<p>Made with Automad <?php echo AM_VERSION; ?></p>
</body>
</html>