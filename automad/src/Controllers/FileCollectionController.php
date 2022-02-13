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
use Automad\UI\Utils\Messenger;

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
		$Response = new Response();
		$Messenger = new Messenger();

		if (!$url) {
			$url = AM_DIR_SHARED;
		}

		Debug::log($_POST);

		if ($delete = Request::post('delete')) {
			FileCollectionModel::deleteFiles(array_keys($delete), $path, $Messenger);
		}

		$data = array('files' => FileCollectionModel::list($path, $url));
		$Response->setData($data);
		$Response->setError($Messenger->getError());
		$Response->setSuccess($Messenger->getSuccess());

		return $Response;
	}

	/**
	 * Upload controller based on $_POST and $_FILES.
	 *
	 * @return Response the response object
	 */
	public static function upload() {
		Debug::log($_POST + $_FILES, 'file');

		if (!empty($_FILES['file']) && is_array($_FILES['file'])) {
			$Cache = new Cache();
			$Automad = $Cache->getAutomad();
			$Messenger = new Messenger();

			$chunk = (object) array_merge(
				array(
					'dzchunkindex' => '',
					'dzchunkbyteoffset' => '',
					'dzchunksize' => 0,
					'dztotalchunkcount' => 0,
					'dzuuid' => '',
					'tmp_name' => '',
					'name' => ''
				),
				$_FILES['file'] + $_POST
			);

			FileCollectionModel::upload(
				$chunk,
				FileSystem::getPathByPostUrl($Automad),
				$Messenger
			);

			$Response = new Response();
			$Response->setError($Messenger->getError());

			return $Response;
		}
	}
}
