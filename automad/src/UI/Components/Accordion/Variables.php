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

namespace Automad\UI\Components\Accordion;

use Automad\Core\Automad;
use Automad\Types\Theme;
use Automad\UI\Components\Form\Group;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The variable accordion item component.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Variables {
	/**
	 * A group of variable fields in an accordion.
	 *
	 * @param Automad $Automad
	 * @param array $keys
	 * @param array $data
	 * @param Theme|null $Theme
	 * @param string $title
	 * @return string the rendered accordion item
	 */
	public static function render(Automad $Automad, array $keys, array $data, ?Theme $Theme, string $title) {
		if (empty($keys)) {
			return '';
		}

		$fn = function ($expression) {
			return $expression;
		};

		return <<< HTML
			<div class="uk-accordion-title">
				$title &mdash;
				{$fn(count($keys))}
			</div>
			<div class="uk-accordion-content">
				{$fn(Group::render($Automad, $keys, $data, false, $Theme))}
			</div>
		HTML;
	}
}
