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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Engine;

use Automad\Core\FileSystem;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The feature provider class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class FeatureProvider {
	/**
	 * An array with names of all existing feature processor classes.
	 */
	private static $processorClasses = array();

	/**
	 * Return the array of feature processor class names.
	 *
	 * @return array the class name array
	 */
	public static function getProcessorClasses() {
		if (empty(self::$processorClasses)) {
			self::$processorClasses = self::findProcessorClasses();
		}

		return self::$processorClasses;
	}

	/**
	 * Find all existing feature processors.
	 *
	 * @return array the class name array
	 */
	private static function findProcessorClasses() {
		$files = FileSystem::glob(__DIR__ . '/Processors/Features/*.php');

		foreach ($files as $file) {
			require_once $file;
		}

		$processorClasses = array_filter(get_declared_classes(), function ($cls) {
			return (strpos($cls, 'Engine\Processors\Features') !== false && strpos($cls, 'Abstract') === false);
		});

		return $processorClasses;
	}
}
