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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Ai\Mcp\Resources;

use Automad\System\PackageCollection;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The filelist templates resource.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class TemplatesFilelist extends AbstractResource {
	/**
	 * @return string
	 */
	public function getDescription(): string {
		return 'A list of available filelist templates.';
	}

	/**
	 * @return callable
	 */
	public function getHandler(): callable {
		return function () {
			return array_map(function ($file) {
				return array(
					'file' => $file,
					'source' => preg_replace('/\<#.*?#\>/s', '', strval(file_get_contents(AM_BASE_DIR . AM_DIR_PACKAGES . $file)))
				);
			}, PackageCollection::getPackagesDirectoryFilelistTemplates());
		};
	}

	/**
	 * The resource's name.
	 *
	 * @return string
	 */
	public function getName(): string {
		return 'templates/filelist';
	}

	/**
	 * @return string
	 */
	public function getTitle(): string {
		return 'Templates / Filelist';
	}
}
