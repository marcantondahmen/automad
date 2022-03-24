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

namespace Automad\Admin\UI\Utils;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * A collection of strings the define URL sections to identify tabs or menu items consistently.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class SwitcherSections {
	/**
	 * The URL sections used in the UI.
	 */
	private static $sections = array(
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
			'settings' => 'settings',
			'text' => 'text',
			'colors' => 'colors',
			'files' => 'files'
		)
	);

	/**
	 * Get a std class object of the sections.
	 *
	 * @return object the sections object
	 */
	public static function get() {
		return json_decode(json_encode(self::$sections));
	}
}
