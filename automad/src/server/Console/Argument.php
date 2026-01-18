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
	 * True if argument is used.
	 * This can be used to identify flags that don't have a value.
	 */
	public bool $isInArgv;

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
	public string $value;

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
		$this->value = '';
		$this->isInArgv = false;
	}
}
