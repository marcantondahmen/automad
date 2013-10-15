<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>$(title)</title>
<meta name="app" content="Automad <?php echo VERSION; ?>">
</head>

<body>
<p>$[navTop]</p>
<p>$[navChildren]</p>
<p>$[navSiblings]</p>	
<h1>$(title)</h1>
<p>$(tags)</p>
<p>$(text)</p>
<p><?php echo $this->P->template; ?></p>
<br />
<p>Made with Automad <?php echo VERSION; ?></p>
</body>
</html>