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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Models\Search;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * A wrapper class for all results for a given data field.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class FieldResults {
	/**
	 * A presenation string of all joined matches with wrapping context.
	 */
	public string $context;

	/**
	 * The field name.
	 */
	public string $field;

	/**
	 * An array with all found matches in the field value.
	 * Note that the matches can differ in case the search value is an unescaped regex string.
	 */
	public array $matches;

	/**
	 * Initialize a new field results instance.
	 *
	 * @param string $field
	 * @param array $matches
	 * @param string $context
	 */
	public function __construct(string $field, array $matches, string $context) {
		$this->field = $field;
		$this->matches = $matches;
		$this->context = $context;
	}
}
