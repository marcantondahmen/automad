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
 * Copyright (c) 2019-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI\Controllers;

use Automad\Core\Cache;
use Automad\Core\Request;
use Automad\UI\Components\Form\HeadlessEditor;
use Automad\UI\Utils\FileSystem;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The headless controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2019-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Headless extends \Automad\Core\Headless {
	/**
	 * Save the updated template or render the editor in case no template was posted.
	 *
	 * @return array $output
	 */
	public static function editTemplate() {
		$output = array();

		if ($template = Request::post('template')) {
			if (FileSystem::write(AM_BASE_DIR . AM_HEADLESS_TEMPLATE_CUSTOM, $template)) {
				Cache::clear();
				$output['success'] = Text::get('success_saved');
			}
		} else {
			$output['html'] = HeadlessEditor::render(self::loadTemplate());
		}

		return $output;
	}

	/**
	 * Get the content of the template in use.
	 *
	 * @return string The content of the template in use
	 */
	public static function loadTemplate() {
		$file = self::getTemplate();

		if (is_readable($file)) {
			return file_get_contents($file);
		}
	}

	/**
	 * Reset the headless template by deleting the custom template file.
	 *
	 * @return array the $output array
	 */
	public static function resetTemplate() {
		$output = array();

		if (Request::post('reset')) {
			if (FileSystem::deleteFile(AM_BASE_DIR . AM_HEADLESS_TEMPLATE_CUSTOM)) {
				Cache::clear();
				$output['trigger'] = 'resetHeadlessTemplate';
				$output['success'] = Text::get('success_reset_headless');
			}
		}

		return $output;
	}
}
