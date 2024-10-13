<?php
$components = array();

foreach (glob(__DIR__ . '/components/*.php') as $file) {
	$name = str_replace('.php', '', basename($file));
	$components[$name] = $file;
}

$loginComponents = array();

foreach (glob(__DIR__ . '/../login/components/*.php') as $file) {
	$name = str_replace('.php', '', basename($file));
	$loginComponents[$name] = $file;
}

$activeSlug = array_keys($components)[0];

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
	<div class="am-l-dashboard">
		<div class="am-l-dashboard__navbar am-l-dashboard__navbar--left" style="z-index: 1100;">
			<div class="am-c-navbar">
				<span class="am-c-navbar__item">
					<am-logo></am-logo>
				</span>
				<span class="am-c-navbar__item" am-tooltip="Add new page">
					<span>New</span>
					<i class="bi bi-plus-lg"></i>
				</span>
			</div>
		</div>
		<div class="am-l-dashboard__navbar am-l-dashboard__navbar--right">
			<div class="am-c-navbar">
				<span class="am-c-navbar__item">
					<span>Search or open</span>
					<span class="am-e-key-combo">⌘ + J</span>
				</span>
				<span class="am-c-navbar__group">
					<span class="am-c-navbar__item" am-tooltip="A system update is available">
						<i class="bi bi-download"></i>
					</span>
					<span class="am-c-navbar__item" am-tooltip="Debugging is enabled"><i class="bi bi-bug"></i></span>
					<span class="am-c-navbar__item"><i class="bi bi-three-dots"></i></span>
				</span>
			</div>
		</div>
		<am-sidebar class="am-l-dashboard__sidebar" style="z-index: 1000;">
			<nav class="am-c-nav">
				<span class="am-c-nav__item">
					<a href="" class="am-c-nav__link">
						<span class="am-c-icon-text">
							<i class="bi bi-window-desktop"></i>
							<span>My Site</span>
						</span>
					</a>
				</span>
				<?php foreach ($themes as $theme) { ?>
					<span class="am-c-nav__item">
						<a href="?t=<?php echo $theme ?>&c=<?php echo $activeSlug; ?>" class="am-c-nav__link">
							<span class="am-c-icon-text">
								<i class="bi bi-palette2"></i>
								<span><?php echo ucwords(str_replace('-', ' ', $theme)); ?></span>
							</span>
						</a>
					</span>
				<?php } ?>
			</nav>
			<nav class="am-c-nav">
				<?php foreach (array_keys($components) as $slug) { ?>
					<span class="am-c-nav__item<?php echo $slug == $activeSlug ? ' am-c-nav__item--active' : ''; ?>">
						<a href="?c=<?php echo $slug ?>&t=<?php echo $activeTheme ?>" class="am-c-nav__link">
							<span class="am-c-icon-text">
								<i class="bi bi-file-text"></i>
								<span><?php echo ucwords(preg_replace('/([A-Z])/', ' $1', $slug)); ?></span>
							</span>
						</a>
					</span>
				<?php } ?>
			</nav>
			<nav class="am-c-nav">
				<?php foreach (array_keys($loginComponents) as $slug) { ?>
					<span class="am-c-nav__item<?php echo $slug == $activeSlug ? ' am-c-nav__item--active' : ''; ?>">
						<a href="../login?c=<?php echo $slug ?>&t=<?php echo $activeTheme ?>" class="am-c-nav__link">
							<span class="am-c-icon-text">
								<i class="bi bi-file-text"></i>
								<span><?php echo ucwords(preg_replace('/([A-Z])/', ' $1', $slug)); ?></span>
							</span>
						</a>
					</span>
				<?php } ?>
			</nav>
		</am-sidebar>
		<div class="am-l-dashboard__main">
			<?php include $components[$activeSlug]; ?>
		</div>
		<footer class="am-l-dashboard__footer">Automad</footer>
	</div>
</body>
</html>
