<?php
$components = array();

foreach (glob(__DIR__ . '/components/*.php') as $file) {
	$name = str_replace('.php', '', basename($file));
	$components[$name] = $file;
}

$activeSlug = 'Button';

if (!empty($_GET['c'])) {
	$slug = $_GET['c'];

	if (array_key_exists($slug, $components)) {
		$activeSlug = $slug;
	}
}

$activeTheme = 'light';

if (!empty($_GET['t'])) {
	$activeTheme = $_GET['t'];
}

$themes = array('light', 'dark', 'low-contrast');
?><!DOCTYPE html>
<html lang="en" class="am-ui <?php echo $activeTheme; ?>">
<?php require __DIR__ . '/../elements/header.php' ?>
<body>
	<div class="am-l-centered">
		<div class="am-l-centered__navbar">
			<div class="am-c-navbar">
				<span>
					<a href="../dashboard?t=<?php echo $activeTheme; ?>" class="am-c-navbar__item">
						My Site
					</a>
				</span>
				<span>
					<a href="../dashboard?t=<?php echo $activeTheme; ?>" class="am-c-navbar__item">
						<i class="bi bi-x"></i>
					</a>
				</span>
			</div>
		</div>
		<div class="am-l-centered__main">
			<div class="am-l-centered__content">
				<?php include $components[$activeSlug]; ?>
			</div>
		</div>
	</div>
</body>
</html>