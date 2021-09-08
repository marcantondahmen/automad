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
use Automad\Engine\Headless;
use Automad\UI\Components\Form\HeadlessEditor;
use Automad\UI\Response;
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
class HeadlessController extends Headless {
	/**
	 * Save the updated template or render the editor in case no template was posted.
	 *
	 * @return Response the response object
	 */
	public static function editTemplate() {
		$Response = new Response();

		if ($template = Request::post('template')) {
			if (FileSystem::write(AM_BASE_DIR . AM_HEADLESS_TEMPLATE_CUSTOM, $template)) {
				Cache::clear();
				$Response->setSuccess(Text::get('success_saved'));
			}
		} else {
			$Response->setHtml(HeadlessEditor::render(self::loadTemplate()));
		}

		return $Response;
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
	 * @return Response the response object
	 */
	public static function resetTemplate() {
		$Response = new Response();

		if (Request::post('reset')) {
			if (FileSystem::deleteFile(AM_BASE_DIR . AM_HEADLESS_TEMPLATE_CUSTOM)) {
				Cache::clear();
				$Response->setTrigger('resetHeadlessTemplate');
				$Response->setSuccess(Text::get('success_reset_headless'));
			}
		}

		return $Response;
	}
}
