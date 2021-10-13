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
use Automad\UI\Components\Form\Group;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The unused variable accordion item component.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class UnusedVariables {
	/**
	 * All unused variable fields in an accordion.
	 *
	 * @param Automad $Automad
	 * @param array $keys
	 * @param array $data
	 * @param string $title
	 * @return string the rendered unused variables accordion item
	 */
	public static function render(Automad $Automad, array $keys, array $data, string $title) {
		$fn = function ($expression) {
			return $expression;
		};

		// Pass the prefix for all IDs related to adding variables
		// according to the IDs defined in JS.
		return <<< HTML
			<div class="uk-accordion-title">
				$title &mdash;
				<span data-am-count="#am-add-variable-container .uk-form-row"></span>
			</div>
			<div class="uk-accordion-content">
				{$fn(Group::render($Automad, $keys, $data, 'am-add-variable'))}
			</div>
		HTML;
	}
}
