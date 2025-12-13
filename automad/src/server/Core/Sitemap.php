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
 * Copyright (c) 2014-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

use Automad\API\RequestHandler;
use Automad\System\Server;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Sitemap class handles the generating process for a site's sitemap.xml.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2014-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Sitemap {
	/**
	 * The constructor verifies, whether sitemap.xml can be written and initiates the generating process.
	 *
	 * @param array $collection
	 */
	public function __construct(array $collection) {
		if (!Session::getUsername()) {
			$this->generate($collection);
		}
	}

	/**
	 * Generate the XML for the sitemap and write sitemap.xml.
	 *
	 * @param array $collection
	 */
	private function generate(array $collection): void {
		if (!$base = AM_BASE_SITEMAP) {
			$base = AM_SERVER . AM_BASE_INDEX;
		}

		$sitemap = AM_BASE_DIR . '/sitemap.xml';
		$xml =  '<?xml version="1.0" encoding="UTF-8"?>' .
				'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		foreach ($collection as $Page) {
			// Only include "real" URLs and not aliases.
			if (strpos($Page->url, '/') === 0) {
				$xml .= "<url><loc>{$base}{$Page->url}</loc></url>";
			}
		}

		$xml .= '</urlset>';

		if (FileSystem::write($sitemap, $xml)) {
			Debug::log($sitemap, 'Successfully generated');
		}

		$server = AM_SERVER;
		$robots = AM_BASE_DIR . '/robots.txt';
		$dashboard = AM_PAGE_DASHBOARD;
		$api = RequestHandler::API_BASE;
		$txt = <<< TXT
			User-agent: *
			Disallow: $dashboard/*
			Disallow: $api/*
			Allow: /

			Sitemap: $server/sitemap.xml
			TXT;

		if (FileSystem::write($robots, $txt)) {
			Debug::log($robots, 'Successfully generated');
		}
	}
}
