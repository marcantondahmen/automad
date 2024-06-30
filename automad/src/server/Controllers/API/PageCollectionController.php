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
 * Copyright (c) 2023-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Controllers\API;

use Automad\API\Response;
use Automad\API\ResponseCache;
use Automad\Core\Automad;
use Automad\Core\FileSystem;
use Automad\Core\Image;
use Automad\Models\Selection;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The oage collection controller handles all requests related to page collections.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2023-2024 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class PageCollectionController {
	/**
	 * Get a list of recently edited pages.
	 *
	 * @return Response the Response object
	 */
	public static function getRecentlyEdited(): Response {
		$ResponseCache = new ResponseCache(function () {
			$Response = new Response();
			$Automad = Automad::fromCache();
			$Selection = new Selection($Automad->getCollection());
			$Selection->sortPages(Fields::TIME_LAST_MODIFIED . ' desc');
			$pages = array_values($Selection->getSelection(false, false, 0, 15));

			return $Response->setData(array_map(function ($Page) {
				$files = FileSystem::glob(AM_BASE_DIR . AM_DIR_PAGES . $Page->path . '*.*');
				$thumbnail = '';
				$images = FileSystem::globGrep(AM_BASE_DIR . AM_DIR_PAGES . $Page->path . '*.*', '/\.(jpe?g|png|webp|gif)$/i');

				if (!empty($images)) {
					$first = reset($images);
					$Image = new Image($first, 400, 300, true);
					$thumbnail = $Image->file;
				}

				return array(
					'title' => $Page->get(Fields::TITLE),
					'url' => $Page->origUrl,
					'lastModified' => $Page->get(Fields::TIME_LAST_MODIFIED),
					'private' => $Page->private,
					'thumbnail' => $thumbnail,
					'fileCount' => count($files)
				);
			}, $pages));
		});

		return $ResponseCache->get();
	}
}
