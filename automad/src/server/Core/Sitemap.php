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
 * Copyright (c) 2014-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

use Automad\System\Server;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Sitemap class handles the generating process for a site's sitemap.xml.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2014-2023 by Marc Anton Dahmen - https://marcdahmen.de
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
			$sitemap = AM_BASE_DIR . '/sitemap.xml';

			// If the base dir is writable without having a sitemap.xml or if sitemap.xml exists and is writable itself.
			if ((is_writable(AM_BASE_DIR) && !file_exists($sitemap)) || is_writable($sitemap)) {
				$this->generate($collection, $sitemap);
			} else {
				Debug::log('Permissions denied!');
			}
		}
	}

	/**
	 * Generate the XML for the sitemap and write sitemap.xml.
	 *
	 * @param array $collection
	 * @param string $sitemap
	 */
	private function generate(array $collection, string $sitemap): void {
		if (!$base = AM_BASE_SITEMAP) {
			$base = AM_SERVER . AM_BASE_INDEX;
		}

		$xml =  '<?xml version="1.0" encoding="UTF-8"?>' .
				'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">';

		foreach ($collection as $Page) {
			$hreflang = '';

			if (AM_I18N_ENABLED && $Page->url !== '/') {
				$lang = I18n::getLanguageFromUrl($Page->url);
				$hreflang = '<xhtml:link rel="alternate" hreflang="' . $lang . '" href="' . $base . $Page->url . '"/>';
			}

			// Only include "real" URLs and not aliases.
			if (strpos($Page->url, '/') === 0) {
				$xml .= "<url><loc>{$base}{$Page->url}</loc>$hreflang</url>";
			}
		}

		$xml .= '</urlset>';

		if (FileSystem::write($sitemap, $xml)) {
			Debug::log($sitemap, 'Successfully generated');
		}
	}
}
