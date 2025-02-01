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
 * Copyright (c) 2023-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Controllers\API;

use Automad\API\Response;
use Automad\Core\FileSystem;
use Automad\Core\Request;
use Automad\Models\Page;
use Automad\Models\Shared;
use Automad\Stores\DataStore;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The page trash model.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2023-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class PageTrashController {
	/**
	 * Clear the trash.
	 *
	 * @return Response
	 */
	public static function clear(): Response {
		$Response = new Response();
		$path = AM_BASE_DIR . AM_DIR_PAGES . Page::TRASH_DIRECTORY;

		FileSystem::trash(array($path));

		return $Response->setRedirect('trash');
	}

	/**
	 * List meta data of delete pages.
	 *
	 * @return Response
	 */
	public static function list(): Response {
		$Response = new Response();
		$path = AM_BASE_DIR . AM_DIR_PAGES . Page::TRASH_DIRECTORY;
		$items = FileSystem::glob("$path/*/" . DataStore::FILENAME);
		$Shared = new Shared();
		$trash = Page::TRASH_DIRECTORY;
		$pages = array();

		foreach ($items as $key => $item) {
			$dir = basename(dirname($item));
			$id = $trash . '/' . $dir;
			$Page = Page::fromDataStore($id, $id, $key, $Shared, $trash, 1);

			if ($Page) {
				$pages[] = array(
					'title' => $Page->get(Fields::TITLE),
					'lastModified' => $Page->get(Fields::TIME_LAST_MODIFIED),
					'path' => $Page->path
				);
			}

			usort($pages, function ($a, $b) {
				if ($a['lastModified'] == $b['lastModified']) {
					return 0;
				}

				return $a < $b ? 1 : -1;
			});
		}

		return $Response->setData($pages);
	}

	/**
	 * Permanently delete a page.
	 *
	 * @return Response
	 */
	public static function permanentlyDelete(): Response {
		$Response = new Response();
		$path = Request::post('path');

		if (!$path) {
			return $Response;
		}

		$fullPath = AM_BASE_DIR . AM_DIR_PAGES . $path;

		if (!is_readable($fullPath) || !is_readable(dirname($fullPath))) {
			return $Response;
		}

		FileSystem::trash(array($fullPath));

		return $Response->setRedirect('trash');
	}

	/**
	 * Restore a given page to the home directory.
	 *
	 * @return Response
	 */
	public static function restore(): Response {
		$Response = new Response();
		$path = Request::post('path');

		$newPath = FileSystem::movePageDir($path, '/', basename($path));

		return $Response->setRedirect(Page::dashboardUrlByPath($newPath));
	}
}
