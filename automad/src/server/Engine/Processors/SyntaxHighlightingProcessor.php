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
 * Copyright (c) 2024-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Engine\Processors;

use Automad\Core\Automad;
use Automad\Engine\Document\Body;
use Automad\Engine\Document\Head;
use Automad\System\Asset;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The syntax highlighting processor class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2024-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class SyntaxHighlightingProcessor {
	/**
	 * The main Automad instance.
	 */
	private Automad $Automad;

	/**
	 * The constructor.
	 *
	 * @param Automad $Automad
	 */
	public function __construct(Automad $Automad) {
		$this->Automad = $Automad;
	}

	/**
	 * Search for pre tags and add Prism assets if needed.
	 *
	 * @param string $str
	 * @return string The processed string
	 */
	public function addAssets(string $str): string {
		if (!preg_match('/\<pre\s*[^>]*\>/', $str) || !preg_match('/class="language-\w+"/', $str)) {
			return $str;
		}

		$theme = $this->Automad->Context->get()->get(Fields::SYNTAX_THEME);

		if ($theme && $theme != 'none') {
			$str = Head::append(
				$str,
				'<link href="https://unpkg.com/automad-prism-themes@latest/dist/prism-' . $theme . '.css" rel="stylesheet" crossorigin="anonymous" referrerpolicy="no-referrer">'
			);
		}

		$str = Head::append($str, Asset::css('dist/prism/main.bundle.css', false));
		$str = Body::append($str, Asset::js('dist/prism/main.bundle.js', false));

		return $str;
	}
}
