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
 * Copyright (c) 2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\API\Models;

use Automad\API\Utils\FileSystem;
use Automad\Core\Page;
use Automad\Core\Str;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The App model handles all data modelling related to page data.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2022 Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class PageModel {
	/**
	 * Extract the deepest directory's prefix from a given path.
	 *
	 * @param string $path
	 * @return string Prefix
	 */
	public static function extractPrefixFromPath(string $path) {
		return substr(basename($path), 0, strpos(basename($path), '.'));
	}

	/**
	 * Extract the slug without the prefix from a given path.
	 *
	 * @param string $path
	 * @return string the slug
	 */
	public static function extractSlugFromPath(string $path) {
		$slug = basename($path);
		$prefix = self::extractPrefixFromPath($slug);

		return Str::stripStart($slug, "$prefix.");
	}

	/**
	 * Return the full file system path of a page's data file.
	 *
	 * @param Page $Page
	 * @return string The full file system path
	 */
	public static function getPageFilePath(Page $Page) {
		return FileSystem::fullPagePath($Page->path) . $Page->template . '.' . AM_FILE_EXT_DATA;
	}
}
