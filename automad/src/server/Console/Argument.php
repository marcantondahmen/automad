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
 * Copyright (c) 2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Console;

defined('AUTOMAD_CONSOLE') or die('Console only!' . PHP_EOL);

/**
 * The console argument type class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Argument {
	/**
	 * The description.
	 */
	public readonly string $description;

	/**
	 * The argument name.
	 */
	public readonly string $name;

	/**
	 * True if the arg is required.
	 */
	public readonly bool $required;

	/**
	 * The value.
	 */
	public string|null $value;

	/**
	 * The constructor.
	 *
	 * @param string $name
	 * @param string $description
	 * @param bool $required
	 */
	public function __construct(string $name, string $description, bool $required = false) {
		$this->name = $name;
		$this->description = $description;
		$this->required = $required;

		// Default to null if arg is used as flag without value.
		$this->value = null;
	}
}
