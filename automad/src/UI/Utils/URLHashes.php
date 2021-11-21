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

namespace Automad\UI\Utils;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * A collection of strings the define URL hashes to identify tabs or menu items consistently.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class URLHashes {
	/**
	 * The URL hashes used in the UI.
	 */
	private static $hashes = array(
		'system' => array(
			'overview' => 'overview',
			'cache' => 'cache',
			'users' => 'users',
			'update' => 'update',
			'feed' => 'feed',
			'language' => 'language',
			'headless' => 'headless',
			'debug' => 'debug',
			'config' => 'config'
		),
		'content' => array(
			'data' => 'data',
			'files' => 'files'
		)
	);

	/**
	 * Get a std class object of the hashes.
	 *
	 * @return object the hashes object
	 */
	public static function get() {
		return json_decode(json_encode(self::$hashes));
	}
}
