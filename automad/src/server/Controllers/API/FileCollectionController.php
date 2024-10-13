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
 * Copyright (c) 2021-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Controllers\API;

use Automad\API\Response;
use Automad\Core\Automad;
use Automad\Core\Debug;
use Automad\Core\FileSystem;
use Automad\Core\Messenger;
use Automad\Core\Request;
use Automad\Core\Text;
use Automad\Models\FileCollection;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The file collection controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2024 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class FileCollectionController {
	/**
	 * Remove selected files from the selection or
	 * simply return the collection of uploaded files for a context.
	 *
	 * @return Response the response object
	 */
	public static function list(): Response {
		$Automad = Automad::fromCache();
		$path = FileSystem::getPathByPostUrl($Automad);

		$Response = new Response();
		$Messenger = new Messenger();

		Debug::log($_POST);

		if ($delete = Request::post('delete')) {
			if (is_array($delete)) {
				FileCollection::deleteFiles(array_keys($delete), $path, $Messenger);
			}
		}

		$data = array('files' => FileCollection::list($path));

		return $Response
			->setData($data)
			->setError($Messenger->getError())
			->setSuccess($Messenger->getSuccess());
	}

	/**
	 * Upload controller based on $_POST and $_FILES.
	 *
	 * @return Response the response object
	 * @psalm-suppress RedundantCondition
	 */
	public static function upload(): Response {
		$Response = new Response();

		if (FileSystem::diskQuotaExceeded()) {
			return $Response->setError(Text::get('diskQuotaExceeded'))->setCode(403);
		}

		Debug::log($_POST + $_FILES, 'file');

		if (!empty($_FILES['file'])) {
			$Automad = Automad::fromCache();
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

			FileCollection::upload(
				$chunk,
				FileSystem::getPathByPostUrl($Automad),
				$Messenger
			);

			$Response->setError($Messenger->getError());
			$Response->setCode($Messenger->getError() ? 406 : 200);
		}

		return $Response;
	}
}
