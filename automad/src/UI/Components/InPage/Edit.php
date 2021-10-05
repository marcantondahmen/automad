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
 * Copyright (c) 2020-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI\Components\InPage;

use Automad\Core\Automad;
use Automad\UI\Components\Form\Field;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The in-page edit component.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Edit {
	/**
	 * 	Create in-page edit field.
	 *
	 * @param Automad $Automad
	 * @param string $key
	 * @param string $value
	 * @param string $context
	 * @param string $path
	 * @return string The HTML for the in-page edit field
	 */
	public static function render(Automad $Automad, string $key, $value, string $context, string $path) {
		$field = Field::render($Automad, $key, $value);
		$label = Field::labelFromKey($key);
		$title = $Automad->getPage($context)->get(AM_KEY_TITLE);

		return <<<HTML
			<div id="am-inpage-edit-fields" data-am-path="$path">
				<div class="am-fullscreen-bar">
					<div class="uk-flex uk-flex-space-between uk-flex-middle uk-height-1-1">
						<div class="uk-flex-item-1 uk-text-truncate uk-margin-small-right">
							<i class="uk-icon-file-text-o"></i>&nbsp;
							$title
							<span class="uk-form-label">$label</span>
						</div>
						<div class="am-icon-buttons">
							<button type="submit" class="uk-button uk-button-success" disabled>
								<i class="uk-icon-check"></i>
							</button>
						</div>
						<button type="button" class="am-fullscreen-bar-button uk-modal-close uk-button">
							<i class="uk-icon-close"></i> 
						</button>
					</div>
				</div>
				<input type="hidden" name="context" value="$context" />
				$field
			</div>
		HTML;
	}
}
