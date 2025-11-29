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
 * Copyright (c) 2024-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Controllers\API;

use Automad\API\Response;
use Automad\Core\Automad;
use Automad\Core\Blocks;
use Automad\Core\Parse;
use Automad\Core\Request;
use Automad\Core\Resolve;
use Automad\Core\Str;
use Automad\Models\Page;
use Automad\Models\Pagelist;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The public controller handles all requests to public handlers.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2024-2025 by Marc Anton Dahmen - https://marcdahmen.de
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
		$shorten = Request::query('shorten');
		$fields =  array_map(
			function (string $field) {
				return preg_replace('/[^\w_\+\:]+/', '', $field);
			},
			Parse::csv(Request::query('fields'))
		);

		$Automad->Context->set($Automad->getPage($context ? $context : '/'));
		$Pagelist = new Pagelist($Automad->getPages(), $Automad->Context);

		$config = array_intersect_key($_GET, $Pagelist->getDefaults());
		$Pagelist->config($config);

		$items = !empty($fields) ? array_map(
			function (Page $Page) use ($fields, $shorten, $Automad) {
				$content = array();

				foreach ($fields as $field) {
					if ($field) {
						if (strpos($field, '+') === 0) {
							$value = Blocks::render($Page->get($field, true), $Automad);
							$content[$field] = $shorten ? html_entity_decode(Str::shorten($value, $shorten)) : $value;
						} elseif (strpos($field, 'text') === 0) {
							$value = Str::markdown($Page->get($field));
							$content[$field] = $shorten ? html_entity_decode(Str::shorten($value, $shorten)) : $value;
						} else {
							$value = $Page->get($field);
							$content[$field] = $value;
						}
					}
				}

				return $content;
			},
			$Pagelist->getPages()
		) : $Pagelist->getPages();

		$items = array_map(function ($item) {
			$item[Fields::URL] = Resolve::absoluteUrlToRoot($item[Fields::URL]);

			return $item;
		}, $items);

		return $Response->setData($items);
	}
}
