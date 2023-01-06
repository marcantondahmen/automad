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

namespace Automad\Admin\Controllers;

use Automad\Admin\API\Response;
use Automad\Admin\Models\ImageCollectionModel;
use Automad\Core\Automad;
use Automad\Core\FileSystem;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The image collection controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2022 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class ImageCollectionController {
	/**
	 * Get a list of shared or page image files
	 *
	 * @return Response the response object
	 */
	public static function list() {
		$Automad = Automad::fromCache();
		$path = FileSystem::getPathByPostUrl($Automad);
		$Response = new Response();

		return $Response->setData(
			array('images' => ImageCollectionModel::list($path))
		);
	}
}
