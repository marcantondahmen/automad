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
 * Copyright (c) 2020-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Blocks;

use Automad\Core\Automad;
use Automad\Engine\Processors\TemplateProcessor;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The snippet block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Snippet {
	/**
	 * This variable tracks whether a snippet is called by another snippet to prevent inifinte recursive loops.
	 */
	public static bool $snippetIsRendering = false;

	/**
	 * Render a snippet block.
	 *
	 * @param array{id: string, data: array{file: string, snippet: string}} $block
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(array $block, Automad $Automad): string {
		// Prevent infinite recursion.
		if (self::$snippetIsRendering) {
			return '';
		}

		self::$snippetIsRendering = true;

		$output = '';
		$data = $block['data'];
		$TemplateProcessor = TemplateProcessor::create($Automad);

		if (!empty($data['snippet'])) {
			$output .= $TemplateProcessor->process($data['snippet'], AM_BASE_DIR . AM_DIR_PACKAGES);
		}

		if (!empty($data['file'])) {
			// Test for files with or without leading slash.
			$file = AM_BASE_DIR . '/' . trim($data['file'], '/');

			if (!is_readable($file)) {
				// Test also path without packages directory.
				$file = AM_BASE_DIR . AM_DIR_PACKAGES . '/' . trim($data['file'], '/');
			}

			if (is_readable($file)) {
				$template = $Automad->loadTemplate($file);
				$output .= $TemplateProcessor->process($template, dirname($file));
			}
		}

		self::$snippetIsRendering = false;

		return $output;
	}
}
