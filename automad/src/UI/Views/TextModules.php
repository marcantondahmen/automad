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

namespace Automad\UI\Views;

use Automad\Core\Config;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The text modules JS file.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class TextModules extends AbstractView {
	public function render() {
		$json = json_encode(Text::getObject());
		$configMTime = gmdate('D, d M Y H:i:s', filemtime(Config::$file)) . ' GMT';
		$etag = md5($json);
		$ifModSince = getenv('HTTP_IF_MODIFIED_SINCE');
		$ifNoneMatch = trim(
			str_replace(
				array('"', '\''),
				'',
				getenv('HTTP_IF_NONE_MATCH')
			)
		);

		header('Cache-Control: public');
		header('Content-Type: application/javascript');

		if (($ifNoneMatch == $etag || !$ifNoneMatch) && ($ifModSince && $ifModSince == $configMTime)) {
			header('HTTP/1.1 304 Not Modified');
			exit();
		}

		header('Last-Modified: ' . $configMTime);
		header('Etag: ' . $etag);

		return <<< JS
			((Automad) => {
				Automad.textModules = $json
			})(window.Automad = window.Automad || {});
		JS;
	}

	protected function body() {
	}

	protected function title() {
	}
}
