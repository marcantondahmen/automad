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
 * Copyright (c) 2021-2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI\Views;

use Automad\UI\Utils\SwitcherSections;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The bootstrap JS file containing all required settings for the UI.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2022 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class BootstrapClient extends AbstractView {
	/**
	 * Render the Javascript file content and set caching headers accordingly
	 *
	 * @return string the Javascript file
	 */
	public function render() {
		$js = $this->compileJS();
		$etag = md5($js);
		$ifNoneMatch = trim(
			str_replace(
				array('"', '\''),
				'',
				getenv('HTTP_IF_NONE_MATCH')
			)
		);

		header('Content-Type: application/javascript');
		header('Cache-Control: max-age=86400');

		if ($ifNoneMatch == $etag) {
			header('HTTP/1.1 304 Not Modified');
			exit();
		}

		header('Etag: ' . $etag);

		return $js;
	}

	protected function body() {
	}

	protected function title() {
	}

	/**
	 * Compile the Javascript file including all bootstrap information.
	 *
	 * @return string the Javascript file content
	 */
	private function compileJS() {
		$fn = $this->fn;

		return <<< JS
			(() => {
				window.Automad = {
					baseURL: '{$fn(AM_BASE_INDEX)}',
					dashboardURL: '{$fn(AM_BASE_INDEX . AM_PAGE_DASHBOARD)}',
					sections: {$fn(json_encode(SwitcherSections::get()))},
					textModules: {$fn(json_encode(Text::getObject()))}
				}
			})();
			JS;
	}
}
