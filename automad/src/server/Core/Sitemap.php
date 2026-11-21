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
 * Copyright (c) 2014-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Core;

use Automad\Api\RequestHandler;
use Automad\Models\Page;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Sitemap class generates the content of a site's sitemap.xml and robots.txt.
 * Both are generated on request in order to always use the host of the actual request
 * and to not persist any request data on disk.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2014-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Sitemap {
	/**
	 * Generate the robots.txt.
	 *
	 * @return string
	 */
	public function getRobots(): string {
		$sitemap = $this->getBase() . '/sitemap.xml';
		$dashboard = AM_PAGE_DASHBOARD;
		$api = RequestHandler::API_BASE;

		return <<< TXT
			User-agent: *
			Disallow: $dashboard/*
			Disallow: $api/*
			Allow: /

			Sitemap: $sitemap
			TXT;
	}

	/**
	 * Generate the sitemap.xml for all public pages of a collection.
	 *
	 * @param array<Page> $collection
	 * @return string
	 */
	public function getXml(array $collection): string {
		$base = $this->getBase();
		$xml =  '<?xml version="1.0" encoding="UTF-8"?>' .
				'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		foreach ($collection as $Page) {
			// Only include "real" URLs and not aliases and skip private pages
			// that are part of the collection for authenticated users.
			if ($Page->url === $Page->get(Fields::ORIG_URL) && !$Page->private) {
				$xml .= "<url><loc>{$base}{$Page->url}</loc></url>";
			}
		}

		return $xml . '</urlset>';
	}

	/**
	 * Get the base URL for all URLs in the sitemap.xml and robots.txt.
	 *
	 * @return string
	 */
	private function getBase(): string {
		return AM_BASE_SITEMAP ?: AM_SERVER . AM_BASE_INDEX;
	}
}
