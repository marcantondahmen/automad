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

use Automad\Core\Config;
use Automad\Core\Str;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The config file edit modal.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class EditConfig {
	/**
	 * Renders the config file edit modal.
	 *
	 * @param string $id
	 * @return string The rendered HTML
	 */
	public static function render(string $id) {
		$Text = Text::getObject();
		$file = Str::stripStart(Config::$file, AM_BASE_DIR);

		return <<< HTML
			<div id="$id" class="uk-modal">
				<div class="am-modal-dialog-code uk-modal-dialog uk-modal-dialog-large">
					<div class="uk-margin-small-bottom uk-grid uk-flex uk-flex-middle" data-uk-grid-margin>
						<div class="uk-width-small-1-1 uk-flex-item-1">
							<span class="uk-badge uk-badge-success uk-badge-notification uk-text-truncate uk-hidden-small">
								<i class="uk-icon-file-text-o"></i>&nbsp;
								$file
							</span>
						</div>
						<div class="uk-flex">
							<a href="#" class="uk-button uk-modal-close">
								<i class="uk-icon-close"></i>&nbsp;
								$Text->btn_close
							</a>
							<button class="uk-button uk-button-success" data-am-submit="Config::save">
								<i class="uk-icon-check"></i>&nbsp;
								$Text->btn_save
							</button>
						</div>
					</div>
					<form 
					class="uk-form" 
					data-am-controller="Config::save"
					data-am-init
					></form>
				</div>
			</div>
		HTML;
	}
}
