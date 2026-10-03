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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Ai\Mcp;

use Automad\Ai\Mcp\Tools\AbstractTool;
use Automad\System\FileSystem;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * Discovers MCP tools and resources. Similar to Automad\Engine\FeatureProvider, this class finds
 * classes by including all files in the Tools and Resources subdirectories and then filtering the
 * declared classes by the Tool/Resource abstractClass they implement, instead of requiring the tools
 * and resources themselves to be manually registered or annotated with attributes.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Provider {
	/**
	 * Return all discovered tools.
	 *
	 * @param bool $isAuthenticated
	 * @return AbstractTool[]
	 */
	public static function getTools(bool $isAuthenticated): array {
		return self::instantiate(self::discover('Tools', AbstractTool::class, $isAuthenticated));
	}

	/**
	 * Find all classes in the given subdirectory that implement the Discoverable interface.
	 *
	 * @param string $dir
	 * @param bool $isAuthenticated
	 * @param string $abstractClass
	 * @return array
	 */
	private static function discover(string $dir, string $abstractClass, bool $isAuthenticated): array {
		$files = FileSystem::glob(__DIR__ . "/$dir/*.php");

		foreach ($files as $file) {
			require_once $file;
		}

		$classes = array_filter(get_declared_classes(), function ($class) use ($abstractClass) {
			return is_subclass_of($class, Discoverable::class) && is_subclass_of($class, $abstractClass);
		});

		if ($isAuthenticated) {
			return $classes;
		}

		return array_filter($classes, fn ($class) => !$class::requiresAuth());
	}

	/**
	 * Create an instance for every given class name.
	 *
	 * @param array $classes
	 * @return array
	 */
	private static function instantiate(array $classes): array {
		return array_map(function ($class) {
			return new $class();
		}, $classes);
	}
}
