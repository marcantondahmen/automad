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

namespace Automad\System\Ai\Mcp;

use Automad\System\FileSystem;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * Discovers MCP tools and resources. Similar to Automad\Engine\FeatureProvider, this class finds
 * classes by including all files in the Tools and Resources subdirectories and then filtering the
 * declared classes by the Tool/Resource interface they implement, instead of requiring the tools
 * and resources themselves to be manually registered or annotated with attributes.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Provider {
	/**
	 * The array of discovered resource instances.
	 */
	private static array $resources = array();

	/**
	 * The array of discovered tool instances.
	 */
	private static array $tools = array();

	/**
	 * Return all discovered resources.
	 *
	 * @return resource[]
	 */
	public static function getResources(): array {
		if (empty(self::$resources)) {
			self::$resources = self::instantiate(self::discover('Resources', Resource::class));
		}

		return self::$resources;
	}

	/**
	 * Return all discovered tools.
	 *
	 * @return Tool[]
	 */
	public static function getTools(): array {
		if (empty(self::$tools)) {
			self::$tools = self::instantiate(self::discover('Tools', Tool::class));
		}

		return self::$tools;
	}

	/**
	 * Find all classes in the given subdirectory that implement the given interface.
	 *
	 * @param string $dir
	 * @param string $interface
	 * @return array
	 */
	private static function discover(string $dir, string $interface): array {
		$files = FileSystem::glob(__DIR__ . "/$dir/*.php");

		foreach ($files as $file) {
			require_once $file;
		}

		return array_filter(get_declared_classes(), function ($class) use ($interface) {
			return is_subclass_of($class, $interface);
		});
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
