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
 * Copyright (c) 2024-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Stores;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * A store class handles the reading of JSON formatted data files.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2024-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class DataStore extends AbstractStore {
	const FILENAME = 'data';

	/**
	 * This method is required to set the actual file path for the store on disk.
	 *
	 * @param ?string $optionalPath
	 * @return string
	 */
	protected function resolvePath(?string $optionalPath = null): string {
		$path = !is_null($optionalPath) ? AM_DIR_PAGES . $optionalPath : AM_DIR_SHARED;
		$path = rtrim(AM_BASE_DIR . $path, '/') . '/';

		return $path . self::FILENAME;
	}
}
