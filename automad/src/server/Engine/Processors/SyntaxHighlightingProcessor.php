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
 * Copyright (c) 2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Engine\Processors;

use Automad\Engine\Document\Body;
use Automad\Engine\Document\Head;
use Automad\System\Asset;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The syntax highlighting address processor class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2024 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class SyntaxHighlightingProcessor {
	/**
	 * The constructor.
	 */
	public function __construct() {
	}

	/**
	 * Search for pre tags and add Prism assets if needed.
	 *
	 * @param string $str
	 * @return string The processed string
	 */
	public function addAssets(string $str): string {
		if (!preg_match('/\<pre\s*[^>]*\>/', $str)) {
			return $str;
		}

		$str = Head::prepend($str, Asset::css('dist/prism/main.bundle.css', false));
		$str = Body::append($str, Asset::js('dist/prism/main.bundle.js', false));

		return $str;
	}
}
