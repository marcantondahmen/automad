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

use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The upload modal component.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Upload {
	/**
	 * Create the upload modal dialog.
	 *
	 * @param string $url
	 * @return string The HTML for the modal dialog
	 */
	public static function render(string $url = '') {
		$Text = Text::getObject();

		return <<< HTML
			<div 
			id="am-upload-modal" 
			class="uk-modal" 
			data-am-url="$url" 
			data-am-dropzone-text="$Text->dropzone" 
			data-am-browse-text="$Text->btn_browse"
			>
				<div class="uk-modal-dialog">
					<div class="uk-modal-header">
						$Text->btn_upload
						<button type="button" class="uk-modal-close uk-close"></button>
					</div>
					<div id="am-upload-container"></div>
					<div class="uk-modal-footer uk-text-right">
						<button type="button" class="uk-modal-close uk-button">
							<i class="uk-icon-close"></i>&nbsp;
							$Text->btn_close
						</button>
					</div>
				</div>
			</div>
		HTML;
	}
}
