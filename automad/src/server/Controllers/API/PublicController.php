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
 * Copyright (c) 2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Controllers\API;

use Automad\API\Response;
use Automad\Core\Automad;
use Automad\Core\Parse;
use Automad\Core\Request;
use Automad\Models\Page;
use Automad\Models\Pagelist;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The public controller handles all requests to public handlers.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2024 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class PublicController {
	/**
	 * Return a pagelist.
	 *
	 * @return Response the response object
	 */
	public static function pagelist(): Response {
		$Response = new Response();
		$Automad = Automad::fromCache();
		$context = Request::query('context');
		$fields =  array_map(
			function (string $field) {
				return preg_replace('/[^\w_\+\:]+/', '', $field);
			},
			Parse::csv(Request::query('fields'))
		);

		$Automad->Context->set($Automad->getPage($context ? $context : '/'));
		$Pagelist = new Pagelist($Automad->getCollection(), $Automad->Context);

		$config = array_intersect_key($_GET, $Pagelist->getDefaults());
		$Pagelist->config($config);

		$pages = !empty($fields) ? array_map(
			function (Page $Page) use ($fields) {
				$content = array();

				foreach($fields as $field) {
					$content[$field] = $Page->get($field);
				}

				return $content;
			},
			$Pagelist->getPages()
		) : $Pagelist->getPages();

		return $Response->setData($pages);
	}
}
