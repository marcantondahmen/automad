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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Controllers;

use Automad\Core\Cache;
use Automad\Core\Sitemap;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Sitemap controller renders the sitemap.xml and robots.txt.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class SitemapController {
	/**
	 * Render the robots.txt.
	 *
	 * @return string
	 */
	public static function robots(): string {
		header('Content-Type: text/plain; charset=UTF-8');

		return (new Sitemap())->getRobots();
	}

	/**
	 * Render the sitemap.xml.
	 *
	 * @return string
	 */
	public static function sitemap(): string {
		header('Content-Type: application/xml; charset=UTF-8');

		$Cache = new Cache();

		return (new Sitemap())->getXml($Cache->getAutomad()->getPages());
	}
}
