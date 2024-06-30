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
 * Copyright (c) 2023-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Value class handles transformation of values.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2023-2024 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Value {
	/**
	 * Return a normalized editor object.
	 *
	 * @param mixed $value
	 * @return array{blocks: array<array-key, mixed>, ...<array-key, mixed>} $data
	 */
	public static function asEditorArray(mixed $value): array {
		if (is_array($value) && isset($value['blocks']) && is_array($value['blocks'])) {
			return $value;
		}

		return array('blocks' => array());
	}

	/**
	 * Convert a mixed value to a string.
	 *
	 * @param mixed $value
	 * @return string
	 */
	public static function asString(mixed $value): string {
		if (is_string($value)) {
			return $value;
		}

		if (is_bool($value)) {
			return strval($value);
		}

		if (is_numeric($value)) {
			// Also handle 0 to be returned as "0".
			return json_encode($value);
		}

		return '';
	}
}
