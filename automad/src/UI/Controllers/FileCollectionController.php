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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI\Controllers;

use Automad\Core\Debug;
use Automad\Core\FileUtils;
use Automad\Core\Request;
use Automad\UI\Components\Layout\FileCollection;
use Automad\UI\Models\FileCollectionModel;
use Automad\UI\Response;
use Automad\UI\Utils\FileSystem;
use Automad\UI\Utils\Text;
use Automad\UI\Utils\UICache;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The file collection controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class FileCollectionController {
	/**
	 * Remove selected files from the selection or
	 * simply return the collection of uploaded files for a context.
	 *
	 * @return Response the response object
	 */
	public static function edit() {
		$Automad = UICache::get();
		$Response = new Response();
		$url = Request::post('url');

		// Check if file from a specified page or the shared files will be listed and managed.
		// To display a file list of a certain page, its URL has to be submitted along with the form data.
		if (!array_key_exists($url, $Automad->getCollection())) {
			$url = '';
			$modalTitle = Text::get('shared_title');
		} else {
			$modalTitle = $Automad->getPage($url)->get(AM_KEY_TITLE);
		}

		$path = FileSystem::getPathByPostUrl($Automad);

		// Delete files in $_POST['delete'].
		if ($delete = Request::post('delete')) {
			$Response = FileCollectionModel::deleteFiles($delete, $path);
		}

		// Get files for each allowed file type.
		$files = FileSystem::globGrep(
			$path . '*.*',
			'/\.(' . implode('|', FileUtils::allowedFileTypes()) . ')$/i'
		);

		$Response->setHtml(FileCollection::render($files, $url, $modalTitle));

		return $Response;
	}

	/**
	 * Upload controller based on $_POST and $_FILES.
	 *
	 * @return Response the response object
	 */
	public static function upload() {
		$Automad = UICache::get();
		Debug::log($_POST + $_FILES, 'files');

		// Set path.
		// If an URL is also posted, use that URL's page path. Without any URL, the /shared path is used.
		$path = FileSystem::getPathByPostUrl($Automad);

		return FileCollectionModel::upload($_FILES, $path);
	}
}
