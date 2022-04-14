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

namespace Automad\Engine\Collections;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The extension asset collection class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class AssetCollection {
	/**
	 * A multidimensional array for .css and .js files.
	 */
	private static $assets = array();

	/**
	 * Return the asset array.
	 *
	 * @return array the collected assets.
	 */
	public static function get() {
		return self::$assets;
	}

	/**
	 * Multidimensionally merge assets.
	 *
	 * @param array $assets
	 */
	public static function merge(array $assets) {
		// Make sure, $this->assets has a basic structure to enable merging new assets.
		self::$assets = array_merge(array('.css' => array(), '.js' => array()), self::$assets);

		foreach (array('.css', '.js') as $type) {
			if (!empty($assets[$type])) {
				self::$assets[$type] = array_merge(self::$assets[$type], $assets[$type]);
			}
		}
	}
}
