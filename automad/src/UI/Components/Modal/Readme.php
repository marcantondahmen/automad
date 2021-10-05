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

namespace Automad\UI\Components\Modal;

use Automad\Core\Str;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The readme modal component.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Readme {
	/**
	 * Create the readme modal dialog.
	 *
	 * @param string $id
	 * @param string $readme
	 * @return string The HTML for the modal dialog
	 */
	public static function render(string $id, string $readme) {
		if ($readme) {
			$Text = Text::getObject();
			$readme = Str::markdown(file_get_contents($readme));

			return <<< HTML
				<div id="$id" class="uk-modal">
					<div class="uk-modal-dialog uk-modal-dialog-large">
						<div class="uk-modal-header">
							Readme
							<a href="#" class="uk-modal-close uk-close"></a>
						</div>
						<div class="am-text">
							$readme
						</div>
						<div class="uk-modal-footer uk-text-right">
							<button 
							class="uk-modal-close uk-button"
							>
								<i class="uk-icon-close"></i>&nbsp;
								$Text->btn_close
							</button>
						</div>
					</div>
				</div>
			HTML;
		}
	}
}
