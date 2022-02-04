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
 * Copyright (c) 2021-2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Controllers;

use Automad\API\Response;
use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\FileSystem;
use Automad\Core\Request;
use Automad\Models\FileCollectionModel;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The file collection controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2022 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class FileCollectionController {
	/**
	 * Remove selected files from the selection or
	 * simply return the collection of uploaded files for a context.
	 *
	 * @return Response the response object
	 */
	public static function list() {
		$Cache = new Cache();
		$Automad = $Cache->getAutomad();
		$path = FileSystem::getPathByPostUrl($Automad);
		$url = Request::post('url');

		if (!$url) {
			$url = AM_DIR_SHARED;
		}

		Debug::log($_POST);

		if ($delete = Request::post('delete')) {
			$Response = FileCollectionModel::deleteFiles($delete, $path);
		} else {
			$Response = new Response();
		}

		$data = array('files' => FileCollectionModel::list($path, $url));
		$Response->setData($data);

		return $Response;
	}

	/**
	 * Upload controller based on $_POST and $_FILES.
	 *
	 * @return Response the response object
	 */
	/* public static function upload() {
		$Automad = UICache::get();
		Debug::log($_POST + $_FILES, 'files');

		// Set path.
		// If an URL is also posted, use that URL's page path. Without any URL, the /shared path is used.
		$path = FileSystem::getPathByPostUrl($Automad);

		return FileCollectionModel::upload($_FILES, $path);
	} */
}
