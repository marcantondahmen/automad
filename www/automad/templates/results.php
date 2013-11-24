<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>p{title}</title>
<meta name="app" content="Automad <?php echo AM_VERSION; ?>">
</head>

<body>
t{navTop}
<h1>p{title}</h1>

t{listSetup(title, subtitle, tags, text)}
t{listFilters}
t{listSortTypes}
t{listSortDirection}
t{listPages}

<br />
<p>Made with Automad <?php echo AM_VERSION; ?></p>
</body>
</html>