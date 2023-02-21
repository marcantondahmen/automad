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
 * Copyright (c) 2023 by Marc Anton Dahmen
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
 * @copyright Copyright (c) 2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Value {
	/**
	 * Return a normalized editor object.
	 *
	 * @param mixed $value
	 * @return object{blocks: array<array-key, object>} $data
	 * @psalm-suppress LessSpecificReturnStatement
	 */
	public static function asEditorObject(mixed $value): object {
		$default = (object) array('blocks' => array());

		if (!is_object($value)) {
			return $default;
		}

		if (!isset($value->blocks)) {
			return $default;
		}

		if (!is_array($value->blocks)) {
			return $default;
		}

		return $value;
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
