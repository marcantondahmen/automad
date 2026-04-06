<?php
/*
 *                    ....
 *                  .:   '':.
 *                  ::::     ':..
 *                  ::.         ''..
 *       .:'.. ..':.:::'    . :.   '':.
 *      :.   ''     ''     '. ::::.. ..:
 *      ::::.        ..':.. .''':::::  .
 *      :::::::..    '..::::  :. ::::  :
 *      ::'':::::::.    ':::.'':.::::  :
 *      :..   ''::::::....':     ''::  :
 *      :::::.    ':::::   :     .. '' .
 *   .''::::::::... ':::.''   ..''  :.''''.
 *   :..:::'':::::  :::::...:''        :..:
 *   ::::::. '::::  ::::::::  ..::        .
 *   ::::::::.::::  ::::::::  :'':.::   .''
 *   ::: '::::::::.' '':::::  :.' '':  :
 *   :::   :::::::::..' ::::  ::...'   .
 *   :::  .::::::::::   ::::  ::::  .:'
 *    '::'  '':::::::   ::::  : ::  :
 *              '::::   ::::  :''  .:
 *               ::::   ::::    ..''
 *               :::: ..:::: .:''
 *                 ''''  '''''
 *
 *
 * AUTOMAD
 *
 * Copyright (c) 2018-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\System;

use Automad\Core\Debug;
use Automad\Core\FileSystem;
use Automad\Core\Str;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The theme collection system class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2018-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class ThemeCollection {
	/**
	 * An array of installed Composer packages.
	 */
	private array $composerInstalled;

	/**
	 * The Theme objects array.
	 */
	private array $themes;

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->composerInstalled = $this->getComposerInstalled();
		$this->themes = $this->collectThemes();
		Debug::log(array_keys($this->themes), 'New instance created');
	}

	/**
	 * 	Get the theme object by the key in the themelist array
	 * 	corresponding to the Fields::THEME variable.
	 *
	 * @param string $key
	 * @return Theme|null The requested theme object
	 */
	public function getThemeByKey(string $key): ?Theme {
		if ($key && array_key_exists($key, $this->themes)) {
			return $this->themes[$key];
		}

		return null;
	}

	/**
	 * Return the Theme objects array.
	 *
	 * @see Theme
	 * @return array The array of Theme objects
	 */
	public function getThemes(): array {
		return $this->themes;
	}

	/**
	 * Collect installed themes recursively.
	 *
	 * A theme must be located below the "themes" directory.
	 * It is possible to group themes in subdirectories, like "themes/theme" or "themes/subdir/theme".
	 *
	 * To be a valid theme, a directory must contain a "theme.json" file and at least one ".php" file.
	 *
	 * @param string|null $path
	 * @return array<string, Theme> An array containing all themes as objects.
	 */
	private function collectThemes(?string $path = null): array {
		if (!$path) {
			$path = AM_BASE_DIR . AM_DIR_PACKAGES;
		}

		$themes = array();

		foreach (FileSystem::glob($path . '/*', GLOB_ONLYDIR) as $dir) {
			if (strpos($dir, 'node_modules') === false) {
				$themeJSON = $dir . '/theme.json';
				$templates = FileSystem::glob($dir . '/*.php');

				if (is_readable($themeJSON) && $templates) {
					// If a theme.json file and at least one .php file exist, use that directoy as a theme.
					$path = Str::stripStart(dirname($themeJSON), AM_BASE_DIR . AM_DIR_PACKAGES . '/');
					$themes[$path] = new Theme($themeJSON, $this->composerInstalled);
				} else {
					// Else check subdirectories for theme.json files.
					$themes = array_merge($themes, $this->collectThemes($dir));
				}
			}
		}

		$uniqueThemes = array();

		// Append path to theme name in case multiple version of the same theme are installed.
		foreach ($themes as $path => $Theme) {
			if (count(array_filter($themes, fn ($th) => $Theme->name == $th->name)) > 1) {
				$uniqueTheme = clone $Theme;

				$uniqueTheme->name = "$Theme->name ($Theme->path)";
				$uniqueThemes[$path] = $uniqueTheme;
			}
		}

		return array_merge($themes, $uniqueThemes);
	}

	/**
	 * Get installed Composer packages.
	 *
	 * @return array An associative array of installed Composer packages
	 */
	private function getComposerInstalled(): array {
		$installedJSON = AM_BASE_DIR . '/vendor/composer/installed.json';
		$installed = array();

		if (is_readable($installedJSON)) {
			$decoded = @json_decode(strval(file_get_contents($installedJSON)), true);

			if (is_array($decoded) && !empty($decoded['packages'])) {
				$packages = $decoded['packages'];
				foreach ($packages as $package) {
					if (array_key_exists('name', $package)) {
						$name = $package['name'];
						$installed[$name] = $package;
					}
				}
			}
		}

		return $installed;
	}
}
