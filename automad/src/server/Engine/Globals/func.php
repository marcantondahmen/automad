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

use Automad\Engine\CustomFunction;

/**
 * A global helper function that can be used in templates to create
 * custom functions that act like extensions. In contrast to a full
 * extensions package, custom functions are not supposed to be
 * distributed as a package but instead provide an easy way
 * to create extended functionality in one single statement
 * real quick without the need of a proper autoload setup
 * and class files.
 *
 * Inside a template thsi function can be used as follows:
 *
 * func('myFunction', function (array $options) {
 *     return json_encode($options);
 * });
 *
 * <@ myFunction { name: value } @>
 *
 * @param string $name
 * @param callable $func
 */
function func(string $name, callable $func): void {
	CustomFunction::add($name, $func);
}
