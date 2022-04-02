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

namespace Automad\Blocks;

use Automad\Core\Automad;
use Automad\Engine\Processors\ContentProcessor;
use Automad\Engine\Processors\TemplateProcessor;
use Automad\Engine\Runtime;
use Automad\UI\InPage;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The snippet block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Snippet {
	/**
	 * This variable tracks whether a snippet is called by another snippet to prevent inifinte recursive loops.
	 */
	private static $snippetIsRendering = false;

	/**
	 * Render a snippet block.
	 *
	 * @param object $data
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(object $data, Automad $Automad) {
		// Prevent infinite recursion.
		if (self::$snippetIsRendering) {
			return '';
		}

		self::$snippetIsRendering = true;

		$Runtime = new Runtime($Automad);
		$InPage = new InPage();

		$ContentProcessor = new ContentProcessor(
			$Automad,
			$Runtime,
			$InPage,
			false
		);

		$TemplateProcessor = new TemplateProcessor(
			$Automad,
			$Runtime,
			$ContentProcessor
		);

		$output = '';

		if (!empty($data->snippet)) {
			$output .= $TemplateProcessor->process($data->snippet, AM_BASE_DIR . AM_DIR_PACKAGES, true);
		}

		if (!empty($data->file)) {
			// Test for files with or without leading slash.
			$file = AM_BASE_DIR . '/' . trim($data->file, '/');

			if (!is_readable($file)) {
				// Test also path without packages directory.
				$file = AM_BASE_DIR . AM_DIR_PACKAGES . '/' . trim($data->file, '/');
			}

			if (is_readable($file)) {
				$template = $Automad->loadTemplate($file);
				$output .= $TemplateProcessor->process($template, dirname($file), true);
			}
		}

		self::$snippetIsRendering = false;

		return $output;
	}
}
